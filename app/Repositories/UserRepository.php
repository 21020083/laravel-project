<?php

namespace App\Repositories;

use App\Common\RoleConst;
use App\Models\User;

class UserRepository extends BaseRepository
{
    protected function getModel()
    {
        return User::class;
    }

    public function search($query, $column, $data)
    {
        return match ($column) {
            'name', 'email', 'username', 'phone_number', 'address' => $query->where($column, 'like', "%${data}%"),
            default => $query,
        };
    }

    public function formatScheduleUserData($userData): bool|string
    {
        $schedule = [
            'shift' => $userData['shift'],
            'working_day' => $userData['working_day'],
        ];

        return json_encode($schedule);
    }
}
