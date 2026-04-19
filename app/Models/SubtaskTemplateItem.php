<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['template_id', 'title', 'description', 'order', 'is_required'])]
class SubtaskTemplateItem extends Model
{
    use HasFactory;

    protected $casts = [
        'is_required' => 'boolean',
    ];

    /**
     * Get the template that owns the item.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(SubtaskTemplate::class);
    }
}
