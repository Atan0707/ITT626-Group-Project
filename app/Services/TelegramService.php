<?php

namespace App\Services;

use App\Models\Package;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private $client;
    private $apiId;
    private $apiHash;
    private $stringSession;

    public function __construct()
    {
        $this->apiId = config('services.telegram.api_id');
        $this->apiHash = config('services.telegram.api_hash');
        $this->stringSession = config('services.telegram.string_session');
    }

    public function sendPackageNotification(Package $package)
    {
        try {
            $expiryDate = Carbon::parse($package->delivery_date)->addWeek()->format('d/m/Y');
            $deliveryDate = Carbon::parse($package->delivery_date)->format('d/m/Y');
            
            $message = "Hi {$package->name}. \n\nYour parcel **{$package->tracking_number}** has been received at Tanjung and is ready for pickup. Please show the reference number to the staff. \n\nReference number: **{$deliveryDate} #{$package->daily_number}** ";
            $message .= "\n\nPlease collect it before **{$expiryDate}** to avoid the item being discarded. Thank you.";

            // Format phone number by adding +6 prefix if not already present
            $phoneNumber = $package->phone_number;
            if (!str_starts_with($phoneNumber, '+6')) {
                $phoneNumber = '+6' . $phoneNumber;
            }

            // Make HTTP POST request to Node.js server
            $response = Http::post('http://localhost:3000/receive-parcel', [
                'phoneNumber' => $phoneNumber,
                'message' => $message
            ]);

            return $response->successful();
        } catch (Exception $e) {
            Log::error('Failed to send Telegram notification: ' . $e->getMessage());
            return false;
        }
    }
} 