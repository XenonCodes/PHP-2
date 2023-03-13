<?php

namespace XenonCodes\PHP2\Blog;

class Like
{
    /**
     * @param UUID $id UUID поста
     * @param Post $post автор поста
     * @param User $author автор поста
     */
    public function __construct(
        private UUID $id,
        private Post $post,
        private User $author
    ) {
    }

    public function __toString()
    {
        return $this->author->getName() . ' поставил like посту:' . PHP_EOL
            . "<<" . $this->post->getTitle() . ">>" . PHP_EOL;
    }

    public function getId(): UUID
    {
        return $this->id;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }
}
