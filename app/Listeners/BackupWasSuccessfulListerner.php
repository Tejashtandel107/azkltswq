<?php

namespace App\Listeners;

use App\Notifications\DatabaseBackupSuccessful;
use Notification;
use Spatie\Backup\Events\BackupWasSuccessful;

class BackupWasSuccessfulListerner
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BackupWasSuccessful $event): void
    {
        $backup_obj = $event->backupDestination->newestBackup();
        $adapter = $event->backupDestination->disk()->getAdapter();
        $client = $adapter->getClient();
        $response = $client->createSharedLinkWithSettings($backup_obj->path());
        if (isset($response) && is_array($response)) {
            if (isset($response['url'])) {
                // $user =User::find(2);
                Notification::route('mail', config('backup.notifications.mail.to'))
                    ->notify(new DatabaseBackupSuccessful($response['url']));
            }
        }
    }
}
