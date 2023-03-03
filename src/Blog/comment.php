<?php

namespace XenonCodes\PHP2\Blog;

class Comment
{
    /**
     * @param UUID $id UUID комментария
     * @param User $author автор комментария
     * @param Post $post пост к которому написан комментарий
     * * @param string $text текст комментария
     */
    public function __construct(
        private UUID $id,
        private User $author,
        private Post $post,
        private string $text
    ) {
    }

    public function __toString()
    {
        return "Оставленные коментарии под постом <<" . $this->post->getTitle() . ">>:" . PHP_EOL
            . '"' . $this->text . PHP_EOL
            . 'Автор: ' . $this->author->getName() . '"';
    }

    public function getId(): UUID
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
