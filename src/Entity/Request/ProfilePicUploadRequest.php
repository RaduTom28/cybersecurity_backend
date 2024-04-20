<?php

namespace App\Entity\Request;

class ProfilePicUploadRequest
{
    private string $profilePicFileName;

    public function getProfilePicFileName(): string
    {
        return $this->profilePicFileName;
    }

    public function setProfilePicFileName(string $profilePicFileName): void
    {
        $this->profilePicFileName = $profilePicFileName;
    }

}