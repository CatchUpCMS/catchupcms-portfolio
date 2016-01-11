<?php namespace Modules\Book\Entities;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'book__statuses';
    protected $fillable = ['name'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
