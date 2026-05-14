function normalizeBasePath(path: string | null | undefined): string {
    const trimmed = String(path ?? '').trim().replace(/^\/+|\/+$/g, '');

    return trimmed ? `/${trimmed}` : '';
}

export function basePath(): string {
    const metaValue = document.querySelector<HTMLMetaElement>('meta[name="base-path"]')?.content;

    return normalizeBasePath(metaValue);
}

export function appPath(path: string): string {
    const base = basePath();

    if (
        path === '' ||
        path.startsWith('#') ||
        path.startsWith('mailto:') ||
        path.startsWith('tel:') ||
        path.startsWith('data:') ||
        path.startsWith('blob:') ||
        /^[a-z][a-z0-9+.-]*:\/\//i.test(path)
    ) {
        return path;
    }

    if (! path.startsWith('/')) {
        return path;
    }

    if (! base || path === base || path.startsWith(`${base}/`)) {
        return path;
    }

    return `${base}${path}`;
}

export function assetPath(path: string): string {
    return appPath(path.startsWith('/') ? path : `/${path}`);
}

export function stripBasePath(path: string): string {
    const base = basePath();

    if (! base) {
        return path;
    }

    if (path === base) {
        return '/';
    }

    return path.startsWith(`${base}/`) ? path.slice(base.length) : path;
}
