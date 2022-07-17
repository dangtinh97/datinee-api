<?php

namespace App\Models;

use App\Traits\SoftDelete\SoftDeletes;
use Jenssegers\Mongodb\Eloquent\Model;

class Attachment extends Model
{
    use SoftDeletes;

    protected $collection = 'dt_attachments';

    protected $fillable = ['path', 'full_url', 'region', 'mime_type', 'bucket_name', 'disk'];
}
