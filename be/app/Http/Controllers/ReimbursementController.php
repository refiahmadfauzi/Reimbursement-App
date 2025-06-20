<?php

namespace App\Http\Controllers;

use App\Models\Reimbursement;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewReimbursementNotification;
use Illuminate\Support\Facades\Auth;

class ReimbursementController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return Reimbursement::withTrashed()
                ->with('user', 'category')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if ($user->role === 'manager') {
            return Reimbursement::where('status', 'pending')
                ->with('user', 'category')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return Reimbursement::where('user_id', $user->id)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'     => 'required',
            'title'       => 'required|string',
            'description' => 'required|string',
            'amount'      => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'file'        => 'nullable|mimes:jpg,png,pdf|max:2048',
        ]);


        $category = Category::find($request->category_id);;
        $totalThisMonth = Reimbursement::where('user_id', $request->user_id)
            ->where('category_id', $request->category_id)
            ->whereMonth('submitted_at', now()->month)
            ->sum('amount');
        if (($totalThisMonth + $request->amount) > $category->limit_per_month) {
            return response()->json(['message' => 'Limit exceeded for this category this month'], 400);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('bukti'), $filename); // simpan ke folder public/bukti
            $filePath = $filename; // hanya simpan nama file, bukan path lengkap
        } else {
            $filePath = null;
        }

        $reim = Reimbursement::create([
            'user_id'     => $request->user_id,
            'title'       => $request->title,
            'description' => $request->description,
            'amount'      => $request->amount,
            'category_id' => $request->category_id,
            'file_path'   => $filePath,
            'submitted_at' => now(),
            'status'      => 'pending',
        ]);

        ActivityLog::create([
            'user_id'    => $request->user_id,
            'action'     => 'Submit',
            'description' => 'Submitted reimbursement #' . $reim->id,
        ]);

        $managers = User::where('role', 'manager')->get();

        foreach ($managers as $manager) {
            Mail::to($manager->email)->queue(new NewReimbursementNotification($reim));
        }


        return response()->json(['message' => 'Reimbursement submitted', 'data' => $reim]);
    }

    public function approve($id)
    {
        $reim = Reimbursement::findOrFail($id);
        $reim->status = 'approved';
        $reim->approved_at = now();
        $reim->save();

        ActivityLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'Approve',
            'description' => 'Approved reimbursement #' . $reim->id,
        ]);

        return response()->json(['message' => 'Approved']);
    }

    public function reject($id)
    {
        $reim = Reimbursement::findOrFail($id);
        $reim->status = 'rejected';
        $reim->save();

        ActivityLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'Reject',
            'description' => 'Rejected reimbursement #' . $reim->id,
        ]);

        return response()->json(['message' => 'Rejected']);
    }

    public function destroy($id)
    {
        $reim = Reimbursement::findOrFail($id);
        $reim->delete();

        ActivityLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'Delete',
            'description' => 'Soft deleted reimbursement #' . $reim->id,
        ]);

        return response()->json(['message' => 'Deleted']);
    }
}
