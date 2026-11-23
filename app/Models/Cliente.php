<?php

namespace App\Models;

use Database\Factories\ClienteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    /** @use HasFactory<ClienteFactory> */
    use HasFactory;

    protected $fillable = ['nome', 'documento', 'email'];

    public function titulos(): HasMany
    {
        return $this->hasMany(Titulo::class);
    }

    /** Documento com máscara: 000.000.000-00 ou 00.000.000/0000-00. */
    public function documentoFormatado(): string
    {
        $d = $this->documento;

        return strlen($d) === 11
            ? vsprintf('%s.%s.%s-%s', [substr($d, 0, 3), substr($d, 3, 3), substr($d, 6, 3), substr($d, 9, 2)])
            : vsprintf('%s.%s.%s/%s-%s', [substr($d, 0, 2), substr($d, 2, 3), substr($d, 5, 3), substr($d, 8, 4), substr($d, 12, 2)]);
    }
}
