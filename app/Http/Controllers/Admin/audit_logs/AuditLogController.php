<?php

namespace App\Http\Controllers\Admin\audit_logs;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a listing of the audit logs.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', 'like', '%' . $request->input('model_type') . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('pages.admin.audit-logs.audit-log-index', compact('logs'));
    }
}
