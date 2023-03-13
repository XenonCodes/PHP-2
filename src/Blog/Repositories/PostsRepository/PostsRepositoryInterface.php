<?php

namespace XenonCodes\PHP2\Blog\Repositories\PostsRepository;

use XenonCodes\PHP2\Blog\Post;
use XenonCodes\PHP2\Blog\UUID;

interface PostsRepositoryInterface
{
    public function save(Post $post): void;
    public function get(UUID $uuid): Post;
    public function delete(UUID $uuid): void;
}
