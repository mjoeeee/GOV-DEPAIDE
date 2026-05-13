<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailManagement extends Model
{
    protected $fillable = [
        'type',
        'school_id',
        'office_id',
        'email_format',
        'reason',
        'attachment',
        'request_id',
        'user_id',
        'firstname',
        'lastname',
        'suffix',
        'position',
        'email',
    ];

    public static function fromDepedEmailRequest(DepedEmailRequest $deped): self
    {
        $model = new self;
        $model->type = 'deped_email';
        $model->school_id = $deped->school_id;
        $model->office_id = $deped->office_id;
        $model->email_format = $deped->email_format;
        $model->request_id = $deped->request_id;
        $model->user_id = $deped->user_id;
        $model->firstname = $deped->firstname;
        $model->lastname = $deped->lastname;
        $model->suffix = $deped->suffix;
        $model->position = $deped->position;

        return $model;
    }

    public static function fromPasswordResetRequest(PasswordResetRequest $reset): self
    {
        $model = new self;
        $model->type = 'email_concern';
        $model->reason = $reset->reason;
        $model->attachment = $reset->attachment;
        $model->request_id = $reset->request_id;
        $model->user_id = $reset->user_id;
        $model->email = $reset->email;

        return $model;
    }

    public function isDepedEmail(): bool
    {
        return $this->type === 'deped_email';
    }

    public function isEmailConcern(): bool
    {
        return $this->type === 'email_concern';
    }
}
