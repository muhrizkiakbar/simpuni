<?php

namespace App\Services;

use App\Models\ArchiveFile;
use App\Models\User;
use App\Repositories\ArchiveFiles;
use Illuminate\Http\Request;
use App\Services\ApplicationService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ArchiveFileService extends ApplicationService
{
    protected $archiveFileRepository;
    protected $currentUser;

    public function __construct(User $user)
    {
        $this->currentUser = $user;
        $this->archiveFileRepository = new ArchiveFiles();
    }

    public function archive_files(Request $request)
    {
        $archive_files = $this->archiveFileRepository->filter(
            $request->all(),
        );
        return $archive_files;
    }

    public function show(string $id)
    {
        return ArchiveFile::find($id);
    }

    public function create($request)
    {
        $archive_file = new ArchiveFile();
        $archive_file->name = $request->name;
        $archive_file->year = $request->year;
        $archive_file->description = $request->description;

        if (!empty($request->attachment) && $request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filePath = $file->store('archive_files/attachment', 'public');

            $archive_file->attachment = $filePath;
            $archive_file->save();
        }

        $archive_file->save();

        return $archive_file;
    }

    public function update(ArchiveFile $archive_file, $request)
    {

        $archive_file->update(
            $request->except(['attachment'])
        );

        $archive_file->save();


        if (!empty($request->attachment) && $request->hasFile('attachment')) {
            if (!is_null($archive_file->attachment)) {
                Storage::delete($archive_file->attachment);
            }

            $file = $request->file('attachment');
            $filePath = $file->store('archive_files/attachment', 'public');

            $archive_file->attachment = $filePath;
            $archive_file->save();
        }

        return $archive_file;
    }

    public function delete(ArchiveFile $archive_file)
    {
        Storage::delete($archive_file->attachment);
        $archive_file->delete();

        return $archive_file;
    }
}
