<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * A user may delete a document only if it is attached to a record in the
     * user's own organisation (per-resource, not just per-route).
     */
    public function delete(User $user, Document $document): bool
    {
        $documentable = $document->documentable;

        if (! $documentable) {
            return false;
        }

        $organisationId = $documentable->organisation_id ?? null;

        return $organisationId !== null
            && $user->organisation_id !== null
            && $user->organisation_id === $organisationId;
    }
}