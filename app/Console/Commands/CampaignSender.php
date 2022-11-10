<?php

namespace App\Console\Commands;

use App\Mail\GenericEmail;
use App\Models\Campaign;
use App\Models\Company;
use App\Models\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CampaignSender extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email campaign';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $campaignModel = new Campaign();
        $today = now();

        $campaigns = $campaignModel::where('schedule_send_date', $today->format('Y-m-d'))
                                //->where('schedule_send_time')
                                ->get();

        foreach ($campaigns as $campaign) {
            $this->info("Campaign to send: $campaign->subject");
            //Todo get contacts by campaign
            $contacts = Lead::where('company_id', 1)
                            ->where('industry_id', 34)
                            ->whereNotNull('email')
                            ->get();
            $this->info('Contacts to send the campaign: '.$contacts->count());
            $company = Company::find($campaign->company_id);

            foreach ($contacts as $contact) {
                $emailTemplate = new GenericEmail($company, $campaign->subject, ['body' => $campaign->body]);
                try {
                    $this->info("Send email to: $contact->email");
                    Mail::to($contact->email)->send($emailTemplate);
                } catch (\Throwable $t) {
                    Log::error($t->getMessage());
                }
            }
        }

        return Command::SUCCESS;
    }
}
