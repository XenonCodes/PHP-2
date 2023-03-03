<?php

namespace XenonCodes\PHP2\Blog;

class Post
{
    /**
     * @param UUID $id UUID поста
     * @param User $author автор поста
     * @param string $title заголовок поста
     * * @param string $text текст поста
     */
    public function __construct(
        private UUID $id,
        private User $author,
        private string $title,
        private string $text
    ) {
    }

    public function __toString()
    {
        return $this->author->getName() . ' пишет:' . PHP_EOL
            . "<<" . $this->title . ">>" . PHP_EOL . $this->text;
    }

    public function getId(): UUID
    {
        return $this->id;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
