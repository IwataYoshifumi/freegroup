<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;
use Carbon\Carbon;

class File extends Models {
    
    protected $fillable = [
                'file_name', 'path', 'user_id',
            ];

    

}
