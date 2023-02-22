<?php

// namespace src\Blog;

namespace XenonCodes\PHP2\Blog;

class Comment
{
    /**
     * @param int $id ID комментария
     * @param User $author автор комментария
     * @param Post $post пост к которому написан комментарий
     * * @param string $text текст комментария
     */
    public function __construct(
        private int $id,
        private User $author,
        private Post $post,
        private string $text
    ) {
    }

    public function __toString()
    {
        return $this->post . PHP_EOL
            . "--------------------------------------------------------" . PHP_EOL
            . "Оставленные коментарии:" . PHP_EOL
            . '"' . $this->text . PHP_EOL
            . 'Автор: ' . $this->author->getName() . '"';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
