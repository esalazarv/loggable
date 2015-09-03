<?php
namespace Esalazarv\Loggable;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
  protected $table = 'record';
  protected $fillable = ['key','old_value','new_value'];
  /**
   * Relationship
   */

  public function loggable(){
    return $this->morphTo();
  }

  public function author(){
    return $this->morphTo();
  }
}
