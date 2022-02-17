<?php

namespace App\Entity;


class Search
{
    private ?string $type = null;
    
    private ?User $user = null;

    private ?string $keywords = null;

    /**
     * Get the value of type
     */ 
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */ 
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of user
     */ 
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Set the value of user
     *
     * @return  self
     */ 
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the value of keywords
     */ 
    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    /**
     * Set the value of keywords
     *
     * @return  self
     */ 
    public function setKeywords(string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }
}