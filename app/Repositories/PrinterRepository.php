<?php

declare(strict_types=1);

namespace Modules\POS\Repositories;

use App\Repositories\BaseEloquentRepository;
use Modules\POS\Contracts\PrinterContract;
use Modules\POS\Models\Printer;

final class PrinterRepository extends BaseEloquentRepository implements PrinterContract
{
    /**
     * Create a new instance of the repository.
     */
    public function __construct()
    {
        $this->model = new Printer();
    }
}
