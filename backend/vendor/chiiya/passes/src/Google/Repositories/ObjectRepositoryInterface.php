<?php declare(strict_types=1);

namespace Chiiya\Passes\Google\Repositories;

use Chiiya\Passes\Common\Component;
use Chiiya\Passes\Google\Passes\AbstractClass;
use Chiiya\Passes\Google\Passes\AbstractObject;

interface ObjectRepositoryInterface
{
    public function index(string $classId, array $parameters = []): Component;

    public function get(string $id): Component;

    public function create(Component $instance): Component;

    public function update(AbstractClass|AbstractObject $instance): Component;
}
