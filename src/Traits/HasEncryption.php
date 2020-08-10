<?php

namespace Bjornvoesten\CipherSweet\Traits;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasEncryption
{
    public static function bootHasEncryption()
    {
        static::saving(function ($model) {
            $model->encrypt(
                array_keys($model->getDirty())
            );
        });
    }

    public function getEncrypted()
    {
        return $this->encrypted ?? [];
    }

    public function encrypt($attributes = ['*'])
    {
        /** @var \Bjornvoesten\CipherSweet\Contracts\Encrypter $encrypter */
        $encrypter = app(
            'Bjornvoesten\CipherSweet\Contracts\Encrypter'
        );

        foreach ($this->getEncrypted() as $column) {
            if (array_search('*', $attributes) !== false
                || array_search($column, $attributes) !== false) {
                /** @var \Illuminate\Database\Eloquent\Model $this */
                $encrypter->encrypt($this, $column);
            }
        }

        return $this;
    }

    public function decrypt($attributes = ['*'])
    {
        /** @var \Bjornvoesten\CipherSweet\Contracts\Encrypter $encrypter */
        $encrypter = app(
            'Bjornvoesten\CipherSweet\Contracts\Encrypter'
        );

        foreach ($this->getEncrypted() as $column) {
            if (array_search('*', $attributes) !== false
                || array_search($column, $attributes) !== false) {
                /** @var \Illuminate\Database\Eloquent\Model $this */
                $encrypter->decrypt($this, $column);
            }
        }

        return $this;
    }

    public function decryptValue($column, $value)
    {
        /** @var \Bjornvoesten\CipherSweet\Contracts\Encrypter $encrypter */
        $encrypter = app(
            'Bjornvoesten\CipherSweet\Contracts\Encrypter'
        );

        return $encrypter->decryptValue($this, $column, $value);
    }

    /**
     * Begin querying the model.
     *
     * @return \Bjornvoesten\CipherSweet\Eloquent\Builder
     */
    public static function query()
    {
        return parent::query();
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Bjornvoesten\CipherSweet\Eloquent\Builder
     */
    public function newEloquentBuilder($query)
    {
        $builder = config(
            'encryption.builder',
            'Bjornvoesten\CipherSweet\Eloquent\Builder'
        );

        return app(
            $builder,
            ['query' => $query]
        );
    }
}
