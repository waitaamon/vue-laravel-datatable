<?php

namespace App\Http\Controllers\DataTable;

use App\User;
use Illuminate\Http\Request;

class UserController extends DataTableController
{
    public function builder()
    {
        return User::query();
    }

    public function getDisplayableColumns()
    {
        return ['id', 'name', 'email', 'created_at'];
    }
    public function getUpdatableColumns()
    {
        return ['name', 'email', 'created_at'];
    }
}
