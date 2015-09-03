<?php namespace Esalazarv\Loggable;
use Auth;
trait LoggableTrait
{
  protected $previousValues = array();

  public static function boot()
  {
      parent::boot();

      if (!method_exists(get_called_class(), 'bootTraits')) {
          static::bootLoggableTrait();
      }
  }

  /**
   * Create the event listeners for the saving and saved events
   * This lets us save revisions whenever a save is made, no matter the
   * http method.
   *
   */
  public static function bootLoggableTrait()
  {
      static::saving(function ($model) {
          $model->beforeSave();
      });

      static::saved(function ($model) {
          $model->afterSave();
      });

      static::deleted(function ($model) {
          $model->beforeSave();
          $model->afterDelete();
      });

  }

  public function record(){
    return $this->morphMany('Esalazarv\Loggable\Record', 'loggable');
  }

  private function beforeSave(){
    $this->previousValues = $this->getOriginal();
  }

  private function afterSave(){
    if(!isset($this->loggingEnabled) || $this->loggingEnabled){
      $changes = $this->getDirty();
      if(isset($this->untrackedFields) && is_array($this->untrackedFields)){
        $changes = array_diff_key($changes,array_flip($this->untrackedFields));
      }
      if(!empty($changes)){
        foreach($changes as $key => $value){
          $record[] = $this->record()->create([
            'key'=>$key,
            'old_value'=>$this->previousValues[$key],
            'new_value'=>$value
          ]);
        }
        if(Auth::check()){
            Auth::user()->activityLog()->saveMany($record);
        }
        unset($changes);
        unset($record);
      }
    }
  }
  
  private function afterDelete(){

  }
}
