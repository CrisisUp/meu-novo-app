<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Exibe a listagem de logs de auditoria (Acesso apenas para admins).
     */
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->when($request->model, function($q, $model) {
                return $q->where('model_type', 'like', "%{$model}%");
            })
            ->when($request->action, function($q, $action) {
                return $q->where('action', $action);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.logs.index', compact('logs'));
    }
}
