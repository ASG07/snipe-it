<?php

namespace App\Http\Controllers\Licenses;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UploadFileRequest;
use App\Models\License;
use App\Models\LicenseSeat;
use Carbon\Carbon;

class LicenseSeatsController extends Controller
{
    /**
     * Show license seats due for audit
     *
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function dueForAudit()
    {
        $this->authorize('audit', License::class);
        
        $settings = \App\Models\Setting::getSettings();
        
        // get counts for badge display
        $total_due_for_audit = LicenseSeat::dueForAudit($settings)->count();
        $total_overdue_for_audit = LicenseSeat::overdueForAudit()->count();
        $total_all_seats = LicenseSeat::allSeats()->count();
        $total_audited_seats = LicenseSeat::audited()->count();
        
        return view('license_seats/audit-due')
            ->with('settings', $settings)
            ->with('total_due_for_audit', $total_due_for_audit)
            ->with('total_overdue_for_audit', $total_overdue_for_audit)
            ->with('total_all_seats', $total_all_seats)
            ->with('total_audited_seats', $total_audited_seats);
    }
    
    /**
     * Show form to audit a specific license seat
     *
     * @param LicenseSeat $licenseSeat
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function audit(LicenseSeat $licenseSeat)
    {
        $this->authorize('audit', License::class);
        
        $settings = \App\Models\Setting::getSettings();
        
        // generate next audit date based on settings
        $dt = Carbon::now()->addMonths($settings->audit_interval)->toDateString();
        
        return view('license_seats/audit')
            ->with('licenseSeat', $licenseSeat)
            ->with('item', $licenseSeat)
            ->with('next_audit_date', $dt);
    }
    
    /**
     * Process the license seat audit form
     *
     * @param Request $request
     * @param LicenseSeat $licenseSeat
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function auditStore(Request $request, LicenseSeat $licenseSeat)
    {
        $this->authorize('audit', License::class);
        
        // store original values for logging
        $originalValues = $licenseSeat->getOriginal();
        
        // update audit dates and notes
        $licenseSeat->next_audit_date = $request->input('next_audit_date');
        $licenseSeat->last_audit_date = Carbon::now();
        $licenseSeat->notes = $request->input('note');
        
        if ($licenseSeat->save()) {
            // log the audit action
            $licenseSeat->logAudit($request->input('note'), null, null, $originalValues);

            if ($request->get('redirect_back') == '1') {
                return redirect()->back()->with('success', trans('admin/licenses/message.audit.success'));
            }
            
            return redirect()->route('licenses.show', $licenseSeat->license_id)
                ->with('success', trans('admin/licenses/message.audit.success'));
        }
        
        return redirect()->back()->withInput()->withErrors($licenseSeat->getErrors());
    }
    
    /**
     * Process bulk audit of license seats
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function bulkAudit(Request $request)
    {
        $this->authorize('audit', License::class);
        
        $seatIds = $request->input('ids');
        
        if (!$seatIds) {
            return redirect()->back()->with('error', trans('admin/licenses/message.audit.error'));
        }
        
        $settings = \App\Models\Setting::getSettings();
        $bulk_action = $request->input('bulk_actions');
        
        // default audit date based on settings
        $dt = Carbon::now()->addMonths($settings->audit_interval)->toDateString();
        
        if ($bulk_action == 'add_notes') {
            // show form to add notes to selected seats (without auditing)
            $seats = LicenseSeat::whereIn('id', $seatIds)->get();
            return view('license_seats/bulk-add-notes')
                ->with('seats', $seats);
        }
        
        // default action is 'audit', show form to set audit note and date
        if ($bulk_action == 'audit') {
            $seats = LicenseSeat::whereIn('id', $seatIds)->get();
            return view('license_seats/bulk-audit')
                ->with('seats', $seats)
                ->with('next_audit_date', $dt);
        }
        
        return redirect()->back()->with('error', trans('admin/licenses/message.audit.error'));
    }
    
    /**
     * store bulk audit
     *
     * @param UploadFileRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function storeBulkAudit(UploadFileRequest $request)
    {
        $this->authorize('audit', License::class);
        
        $seatIds = $request->input('seat_ids');
        $note = $request->input('note') ?: 'Bulk audit';
        $next_audit_date = $request->input('next_audit_date');
        
        if (!$seatIds || !$next_audit_date) {
            return redirect()->back()
                ->with('error', trans('admin/licenses/message.audit.error'))
                ->withInput();
        }
        
        $seats = LicenseSeat::whereIn('id', explode(',', $seatIds))->get();
        $successes = 0;
        $errors = 0;
        
        foreach ($seats as $seat) {
            $originalValues = $seat->getOriginal();
            $seat->next_audit_date = $next_audit_date;
            $seat->last_audit_date = Carbon::now();
            $seat->notes = $note;
            
            if ($seat->save()) {
                $seat->logAudit($note, null, null, $originalValues);
                $successes++;
            } else {
                $errors++;
            }
        }
        
        if ($successes > 0) {
            return redirect()->back()
                ->with('success', trans_choice('admin/licenses/message.bulkaudit.success', $successes, ['count' => $successes]));
        }
        
        return redirect()->back()->with('error', trans('admin/licenses/message.bulkaudit.error'));
    }


    /**
     * store bulk notes for license seats (without auditing)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function storeBulkNotes(Request $request)
    {
        $this->authorize('update', License::class);
        
        $seatIds = $request->input('seat_ids');
        $notes = $request->input('notes');
        $overwrite = $request->input('overwrite', false);
        
        if (!$seatIds || !$notes) {
            return redirect()->back()
                ->with('error', trans('admin/licenses/message.notes.error'))
                ->withInput();
        }
        
        $seats = LicenseSeat::whereIn('id', explode(',', $seatIds))->get();
        $successes = 0;
        $errors = 0;

        foreach ($seats as $seat) {
            // if overwrite is checked or seat has no existing notes, set the new notes, otherwise, add to existing notes
            if ($overwrite || empty($seat->notes)) {
                $seat->notes = $notes;
            } else {
                $seat->notes = $seat->notes . "\n\n" . $notes;
            }
            
            if ($seat->save()) {
                $successes++;
            } else {
                $errors++;
            }
        }
        
        if ($successes > 0) {
            return redirect()->back()
                ->with('success', trans_choice('admin/licenses/message.notes.success', $successes, ['count' => $successes]));
        }
        
        return redirect()->back()->with('error', trans('admin/licenses/message.notes.error'));
    }
}
