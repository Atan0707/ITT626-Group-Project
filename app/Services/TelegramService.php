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
            $deliveryDate = Carbon::parse($package->delivery_date);
            $discardDate = $deliveryDate->copy()->addWeek()->format('d/m/Y');
            $deliveryDateFormatted = $deliveryDate->format('d/m/Y');
            
            $message = "Hi {$package->name}. \n\nYour parcel **{$package->tracking_number}** has been received at **{$package->shop->name}** and is ready for pickup. Please show the reference number to the staff. \n\nReference number: **{$deliveryDateFormatted} #{$package->daily_number}** ";
            $message .= "\n\nPlease collect it before **{$discardDate}** to avoid the item being discarded. Thank you.";

            // Format phone number by adding +6 prefix if not already present
            $phoneNumber = $package->phone_number;
            if (!str_starts_with($phoneNumber, '+6')) {
                $phoneNumber = '+6' . $phoneNumber;
            }

            // Make HTTP POST request to Node.js server
            $response = Http::post('http://167.99.77.31:3001/receive-parcel', [
                'phoneNumber' => $phoneNumber,
                'message' => $message
            ]);

            if (!$response->successful()) {
                Log::error('Failed to send Telegram notification. Response: ' . $response->body());
            }

            return $response->successful();
        } catch (Exception $e) {
            Log::error('Failed to send Telegram notification: ' . $e->getMessage());
            return false;
        }
    }

    public function sendCollectionNotification(Package $package)
    {
        try {
            $collectionTime = Carbon::now()->format('d/m/Y h:i A');
            $message = "Dear {$package->name},\n\nYour parcel with tracking number **{$package->tracking_number}** has been collected on **{$collectionTime}**.\n\nThank you for using our service!";

            // Format phone number by adding +6 prefix if not already present
            $phoneNumber = $package->phone_number;
            if (!str_starts_with($phoneNumber, '+6')) {
                $phoneNumber = '+6' . $phoneNumber;
            }

            // Make HTTP POST request to Node.js server
            $response = Http::post('http://167.99.77.31:3001/receive-parcel', [
                'phoneNumber' => $phoneNumber,
                'message' => $message
            ]);

            return $response->successful();
        } catch (Exception $e) {
            Log::error('Failed to send collection notification: ' . $e->getMessage());
            return false;
        }
    }

    public function sendDiscardNotification(Package $package)
    {
        try {
            $discardDate = $package->discard_date->format('d/m/Y');
            $message = "Dear {$package->name},\n\nYour parcel **{$package->tracking_number}** at **{$package->shop->name}** has been discarded as it was not collected before the discard date (**{$discardDate}**).\n\nPlease contact the shop for further assistance. Thank you.";

            // Format phone number by adding +6 prefix if not already present
            $phoneNumber = $package->phone_number;
            if (!str_starts_with($phoneNumber, '+6')) {
                $phoneNumber = '+6' . $phoneNumber;
            }

            // Make HTTP POST request to Node.js server
            $response = Http::post('http://167.99.77.31:3001/receive-parcel', [
                'phoneNumber' => $phoneNumber,
                'message' => $message
            ]);

            return $response->successful();
        } catch (Exception $e) {
            Log::error('Failed to send discard notification: ' . $e->getMessage());
            return false;
        }
    }

    public function sendReminderNotification(Package $package)
    {
        try {
            $discardDate = $package->discard_date->format('d/m/Y');
            $message = "Dear {$package->name},\n\nThis is a reminder that your parcel **{$package->tracking_number}** is still waiting for collection at **{$package->shop->name}**.\n\nPlease collect it before **{$discardDate}** to avoid the item being discarded.\n\nReference number: **{$package->delivery_date->format('d/m/Y')} #{$package->daily_number}**\n\nThank you.";

            // Format phone number by adding +6 prefix if not already present
            $phoneNumber = $package->phone_number;
            if (!str_starts_with($phoneNumber, '+6')) {
                $phoneNumber = '+6' . $phoneNumber;
            }

            // Make HTTP POST request to Node.js server
            $response = Http::post('http://167.99.77.31:3001/receive-parcel', [
                'phoneNumber' => $phoneNumber,
                'message' => $message
            ]);

            return $response->successful();
        } catch (Exception $e) {
            Log::error('Failed to send reminder notification: ' . $e->getMessage());
            return false;
        }
    }
} 