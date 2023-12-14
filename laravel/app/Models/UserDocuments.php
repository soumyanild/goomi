<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserDocuments extends Model
{
  use HasFactory;
  protected $table = 'user_documents';
  protected $fillable = [
    'user_id',
    'document',
    'status'

  ];
  public function addUserDocument($inputArr)
  {
    return self::create($inputArr);
  }

  public function getDocumentAttribute($value)
  {
    return !empty($value) ? asset($value) : null;
  }

  public function getDocument()
  {
    if (!empty($this->document))
      return url($this->document);
  }


}
