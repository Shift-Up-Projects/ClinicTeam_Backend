<?php

namespace App\Filament\Resources\Doctors\Pages;

use App\Filament\Resources\Doctors\DoctorResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateDoctor extends CreateRecord
{
    protected static string $resource = DoctorResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $record = parent::handleRecordCreation($data);
        $this->record = $record;
        $this->afterCreate();
        return $record;
    }

    public function afterCreate(): void
    {
        // Get the user associated with the doctor
        $user = User::find($this->record->user_id);

        if ($user) {
            // Update the user's role to 'doctor'
            $user->update(['role' => 'doctor']);
        }
    }
}
