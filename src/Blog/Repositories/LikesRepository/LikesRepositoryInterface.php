<?php

namespace XenonCodes\PHP2\Blog\Repositories\LikesRepository;

use XenonCodes\PHP2\Blog\Like;
use XenonCodes\PHP2\Blog\UUID;

interface LikesRepositoryInterface
{
    public function save(Like $like): void;
    public function get(UUID $uuid): Like;
    public function delete(UUID $uuid): void;
    public function getByPostUuid(string $postUuid): array;
}
