<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ResponsibleUserService
{
    private const CACHE_KEY_ALL = 'responsable-users:all';

    public function __construct(private readonly CacheRepository $cache)
    {
    }

    /**
     * Obtiene el query base para usuarios responsables activos.
     */
    public function query(): Builder
    {
        return User::responsables()
            ->select(['id', 'name'])
            ->orderBy('name');
    }

    /**
     * Devuelve todos los responsables, con cache opcional.
     */
    public function all(?int $ttl = null): Collection
    {
        $ttl = $ttl ?? config('tickets.responsables_cache_ttl', 600);

        if ($ttl <= 0) {
            return $this->query()->get();
        }

        return $this->cache->remember(
            self::CACHE_KEY_ALL,
            now()->addSeconds($ttl),
            fn () => $this->query()->get()
        );
    }

    /**
     * Devuelve la lista paginada de responsables.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()->paginate($perPage);
    }

    /**
     * Limpia el cache del listado de responsables.
     */
    public function forgetCache(): void
    {
        $this->cache->forget(self::CACHE_KEY_ALL);
    }
}
