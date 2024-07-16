<?php
declare(strict_types=1);

namespace Project\Bookworm\Model;

interface ForumRepository
{
    public function save(Forum $forum): void;
}
?>