<?php

declare(strict_types=1);

namespace Modules\POS\Repositories;

use App\Repositories\BaseEloquentRepository;
use Modules\POS\Contracts\TerminalContract;
use Modules\POS\Models\Terminal;

final class TerminalRepository extends BaseEloquentRepository implements TerminalContract
{
    /**
     * Create a new instance of the repository.
     */
    public function __construct()
    {
        $this->model = new Terminal();
    }
}
