<?php

namespace App\Repositories;

use App\Common\RoleConst;
use App\Models\ResetPassword;
use App\Models\User;

class ResetPasswordRepository extends BaseRepository
{
    protected function getModel()
    {
        return ResetPassword::class;
    }

    public function search($query, $column, $data)
    {
        return match ($column) {
            'email', 'token' => $query->where($column, 'like', "%${data}%"),
            default => $query,
        };
    }


}

