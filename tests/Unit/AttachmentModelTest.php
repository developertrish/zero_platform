<?php

namespace Tests\Unit;

use App\Models\Attachment;
use Tests\TestCase;

class AttachmentModelTest extends TestCase
{
    public function test_type_from_mime_image(): void
    {
        $this->assertEquals('image', Attachment::typeFromMime('image/jpeg'));
        $this->assertEquals('image', Attachment::typeFromMime('image/png'));
        $this->assertEquals('image', Attachment::typeFromMime('image/gif'));
        $this->assertEquals('image', Attachment::typeFromMime('image/webp'));
    }

    public function test_type_from_mime_video(): void
    {
        $this->assertEquals('video', Attachment::typeFromMime('video/mp4'));
        $this->assertEquals('video', Attachment::typeFromMime('video/quicktime'));
    }

    public function test_type_from_mime_document(): void
    {
        $this->assertEquals('document', Attachment::typeFromMime('application/pdf'));
        $this->assertEquals('document', Attachment::typeFromMime('application/msword'));
        $this->assertEquals('document', Attachment::typeFromMime(
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ));
    }

    public function test_type_from_mime_other(): void
    {
        $this->assertEquals('other', Attachment::typeFromMime('application/zip'));
        $this->assertEquals('other', Attachment::typeFromMime('application/octet-stream'));
    }

    public function test_human_size_bytes(): void
    {
        $att = new Attachment(['size' => 512]);
        $this->assertEquals('512 B', $att->humanSize());
    }

    public function test_human_size_kilobytes(): void
    {
        $att = new Attachment(['size' => 2048]);
        $this->assertEquals('2.0 KB', $att->humanSize());
    }

    public function test_human_size_megabytes(): void
    {
        $att = new Attachment(['size' => 5 * 1024 * 1024]);
        $this->assertEquals('5.0 MB', $att->humanSize());
    }

    public function test_is_image(): void
    {
        $att = new Attachment(['type' => 'image']);
        $this->assertTrue($att->isImage());
        $this->assertFalse($att->isVideo());
    }

    public function test_is_video(): void
    {
        $att = new Attachment(['type' => 'video']);
        $this->assertTrue($att->isVideo());
        $this->assertFalse($att->isImage());
    }
}
