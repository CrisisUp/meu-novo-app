<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Loggable
{
    /**
     * O método boot[TraitName] é chamado automaticamente pelo Laravel.
     */
    protected static function bootLoggable()
    {
        static::created(function ($model) {
            static::logActivity($model, 'created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            // Apenas registra se algo realmente mudou
            $old = array_intersect_key($model->getOriginal(), $model->getChanges());
            $new = $model->getChanges();
            
            if (!empty($new)) {
                static::logActivity($model, 'updated', $old, $new);
            }
        });

        static::deleted(function ($model) {
            $action = method_exists($model, 'isForceDeleting') && $model->isForceDeleting() 
                ? 'deleted' 
                : 'soft_deleted';
                
            static::logActivity($model, $action, $model->getAttributes(), null);
        });
    }

    protected static function logActivity($model, $action, $old = null, $new = null)
    {
        // Remove campos irrelevantes para auditoria (timestamps)
        $ignored = ['created_at', 'updated_at', 'remember_token', 'password'];
        
        if ($old) {
            $old = array_diff_key($old, array_flip($ignored));
        }
        if ($new) {
            $new = array_diff_key($new, array_flip($ignored));
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'action' => $action,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
