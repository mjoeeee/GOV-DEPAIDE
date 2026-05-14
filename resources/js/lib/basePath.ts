const DEFAULT_BASE_PATH = '';

type FetchInput = Parameters<typeof fetch>[0];

type WayfinderRoute = {
    definition?: {
        url?: string;
    };
};

type InertiaRouterLike = Record<string, unknown> & {
    __depaideBasePathConfigured?: boolean;
};

type InertiaRouterMethod = (url: string, ...args: unknown[]) => unknown;

const DOM_URL_ATTRIBUTES = [
    'action',
    'data-lazy-src',
    'data-src',
    'href',
    'poster',
    'src',
];

declare global {
    interface Window {
        __depaideBasePathDomConfigured?: boolean;
    }
}

function normalizeBasePath(basePath: string | undefined): string {
    const normalized = (basePath || DEFAULT_BASE_PATH).trim();

    if (! normalized || normalized === '/') {
        return '';
    }

    return `/${normalized.replace(/^\/+|\/+$/g, '')}`;
}

function metaBasePath(): string {
    if (typeof document === 'undefined') {
        return '';
    }

    return document.querySelector<HTMLMetaElement>('meta[name="base-path"]')?.content ?? '';
}

export const appBasePath = normalizeBasePath(
    import.meta.env.VITE_APP_BASE_PATH ||
        (import.meta.env.BASE_URL !== '/' ? import.meta.env.BASE_URL : '') ||
        metaBasePath(),
);

function hasProtocol(url: string): boolean {
    return /^[a-z][a-z\d+\-.]*:/i.test(url);
}

function isIgnoredUrl(url: string): boolean {
    const trimmed = url.trim();

    return (
        trimmed === '' ||
        trimmed.startsWith('#') ||
        /^(blob|data|javascript|mailto|tel|ws|wss):/i.test(trimmed)
    );
}

function isLocalGeneratedUrl(url: URL): boolean {
    return (
        url.origin === window.location.origin ||
        ['localhost', '127.0.0.1', '::1'].includes(url.hostname)
    );
}

function isAlreadyPrefixed(pathname: string): boolean {
    return (
        appBasePath === '' ||
        pathname === appBasePath ||
        pathname.startsWith(`${appBasePath}/`)
    );
}

function preserveRoutePlaceholders(path: string): string {
    return path.replace(/%7B/gi, '{').replace(/%7D/gi, '}');
}

export function withBasePath(path: string): string {
    if (! path || appBasePath === '') {
        return path;
    }

    if (path.startsWith('//') || hasProtocol(path)) {
        return path;
    }

    if (! path.startsWith('/')) {
        return `${appBasePath}/${path.replace(/^\/+/, '')}`;
    }

    if (isAlreadyPrefixed(path)) {
        return path;
    }

    return `${appBasePath}${path}`;
}

export function asset(path: string): string {
    if (! path || path.startsWith('//') || hasProtocol(path) || isIgnoredUrl(path)) {
        return path;
    }

    return withBasePath(`/${path.replace(/^\/+/, '')}`);
}

export function toApplicationUrl(url: string): string {
    if (typeof window === 'undefined' || ! url) {
        return url;
    }

    if (isIgnoredUrl(url)) {
        return url;
    }

    if (! hasProtocol(url)) {
        if (url.startsWith('//')) {
            const parsed = new URL(`${window.location.protocol}${url}`);
            const path = preserveRoutePlaceholders(`${parsed.pathname}${parsed.search}${parsed.hash}`);

            return isLocalGeneratedUrl(parsed) ? withBasePath(path) : url;
        }

        return withBasePath(url);
    }

    const parsed = new URL(url);

    if (! isLocalGeneratedUrl(parsed)) {
        return url;
    }

    const path = preserveRoutePlaceholders(`${parsed.pathname}${parsed.search}${parsed.hash}`);

    return isAlreadyPrefixed(parsed.pathname) ? path : withBasePath(path);
}

function normalizeSrcset(value: string): string {
    return value
        .split(',')
        .map((candidate) => {
            const parts = candidate.trim().split(/\s+/);
            const url = parts.shift();

            if (! url) {
                return candidate;
            }

            return [toApplicationUrl(url), ...parts].join(' ');
        })
        .join(', ');
}

function normalizeDomAttribute(element: Element, attribute: string): void {
    const currentValue = element.getAttribute(attribute);

    if (! currentValue) {
        return;
    }

    const normalized = attribute === 'srcset'
        ? normalizeSrcset(currentValue)
        : toApplicationUrl(currentValue);

    if (normalized !== currentValue) {
        element.setAttribute(attribute, normalized);
    }
}

function normalizeDomElement(element: Element): void {
    DOM_URL_ATTRIBUTES.forEach((attribute) => {
        normalizeDomAttribute(element, attribute);
    });

    normalizeDomAttribute(element, 'srcset');
}

function normalizeDomTree(root: ParentNode): void {
    if (root instanceof Element) {
        normalizeDomElement(root);
    }

    root.querySelectorAll?.(
        DOM_URL_ATTRIBUTES.map((attribute) => `[${attribute}]`)
            .concat('[srcset]')
            .join(','),
    ).forEach(normalizeDomElement);
}

function configureSetAttributeBasePath(): void {
    const originalSetAttribute = Element.prototype.setAttribute;

    Element.prototype.setAttribute = function (qualifiedName: string, value: string): void {
        const normalizedName = qualifiedName.toLowerCase();
        const normalizedValue = normalizedName === 'srcset'
            ? normalizeSrcset(String(value))
            : DOM_URL_ATTRIBUTES.includes(normalizedName)
                ? toApplicationUrl(String(value))
                : value;

        originalSetAttribute.call(this, qualifiedName, normalizedValue);
    };
}

export function configureDomBasePath(): void {
    if (
        typeof window === 'undefined' ||
        typeof document === 'undefined' ||
        appBasePath === '' ||
        window.__depaideBasePathDomConfigured
    ) {
        return;
    }

    window.__depaideBasePathDomConfigured = true;
    configureSetAttributeBasePath();
    normalizeDomTree(document);

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'attributes') {
                normalizeDomElement(mutation.target as Element);

                return;
            }

            mutation.addedNodes.forEach((node) => {
                if (node instanceof Element) {
                    normalizeDomTree(node);
                }
            });
        });
    });

    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: [...DOM_URL_ATTRIBUTES, 'srcset'],
        childList: true,
        subtree: true,
    });
}

function resolveFetchInput(input: FetchInput): FetchInput {
    if (typeof input === 'string') {
        return toApplicationUrl(input);
    }

    if (input instanceof URL) {
        return new URL(toApplicationUrl(input.toString()));
    }

    if (input instanceof Request) {
        return new Request(toApplicationUrl(input.url), input);
    }

    return input;
}

export function configureFetchBasePath(): void {
    if (typeof window === 'undefined') {
        return;
    }

    const originalFetch = window.fetch.bind(window);

    window.fetch = (input, init) => originalFetch(resolveFetchInput(input), init);
}

export function configureInertiaRouterBasePath(router: InertiaRouterLike): void {
    if (! router || router.__depaideBasePathConfigured) {
        return;
    }

    router.__depaideBasePathConfigured = true;

    ['visit', 'get', 'post', 'put', 'patch', 'delete'].forEach((method) => {
        const originalMethod = router[method];

        if (typeof originalMethod !== 'function') {
            return;
        }

        router[method] = function (this: unknown, url: string, ...args: unknown[]): unknown {
            return (originalMethod as InertiaRouterMethod).call(this, toApplicationUrl(url), ...args);
        };
    });
}

function configureWayfinderRoute(value: unknown): void {
    if (! value || typeof value !== 'function') {
        return;
    }

    const route = value as WayfinderRoute;

    if (typeof route.definition?.url === 'string') {
        route.definition.url = toApplicationUrl(route.definition.url);
    }
}

function configureWayfinderValue(value: unknown): void {
    configureWayfinderRoute(value);

    if (! value || typeof value !== 'object') {
        return;
    }

    Object.values(value).forEach(configureWayfinderValue);
}

export function configureWayfinderBasePath(modules: Record<string, unknown>[]): void {
    modules.forEach((module) => {
        Object.values(module).forEach(configureWayfinderValue);
    });
}

export const appPath = withBasePath;
export const assetPath = asset;

export function basePath(): string {
    return appBasePath;
}

export function stripBasePath(path: string): string {
    if (! appBasePath) {
        return path;
    }

    if (path === appBasePath) {
        return '/';
    }

    return path.startsWith(`${appBasePath}/`) ? path.slice(appBasePath.length) : path;
}
