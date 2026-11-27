<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    public const MAX_SIZE_KB = 10240; // 10MB

    public function upload(Model $documentable, UploadedFile $file, string $type, ?string $expiresAt = null, ?int $verifiedBy = null): Document
    {
        $disk = 'local';
        $directory = 'documents/' . class_basename($documentable) . '/' . $documentable->id;
        // Derive the stored extension from the (validated) MIME type rather
        // than trusting the client-supplied filename extension.
        $extension = $this->extensionForMime($file->getMimeType());
        $filename = Str::uuid() . '.' . $extension;
        $path = $file->storeAs($directory, $filename, $disk);

        return Document::create([
            'documentable_type' => $documentable->getMorphClass(),
            'documentable_id' => $documentable->id,
            'type' => $type,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'expires_at' => $expiresAt,
            'verified_at' => $verifiedBy ? now() : null,
            'verified_by' => $verifiedBy,
        ]);
    }

    public function delete(Document $document): void
    {
        Storage::disk('local')->delete($document->file_path);
        $document->delete();
    }

    public function verify(Document $document, int $verifiedBy): void
    {
        $document->update([
            'verified_at' => now(),
            'verified_by' => $verifiedBy,
        ]);
    }

    /**
     * Map a MIME type to a safe, fixed file extension.
     */
    protected function extensionForMime(string $mime): string
    {
        return match ($mime) {
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            default => 'bin',
        };
    }
}