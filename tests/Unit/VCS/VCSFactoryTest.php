<?php

use Blep\VCS\GitVCS;
use Blep\VCS\SubversionVCS;
use Blep\VCS\VCSFactory;

// Helper to create a temporary directory tree and return its path.
// The caller is responsible for cleanup.
function makeTempDir(): string
{
    $dir = sys_get_temp_dir() . '/blep_vcs_test_' . uniqid();
    mkdir($dir, 0777, true);
    return $dir;
}

function removeTempDir(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }
    foreach (new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    ) as $entry) {
        $entry->isDir() ? rmdir($entry->getPathname()) : unlink($entry->getPathname());
    }
    rmdir($dir);
}

// --- GitVCS::isAvailable ---

it('returns false for a relative path with no VCS — does not loop', function () {
    $root = makeTempDir();
    $original = getcwd();
    chdir($root);

    $result = GitVCS::isAvailable('no/such/relative/path/file.php');

    chdir($original);
    removeTempDir($root);

    expect($result)->toBeFalse();
});

it('detects .git when given an absolute path inside a git repo', function () {
    $root = makeTempDir();
    mkdir($root . '/.git');
    $subdir = $root . '/src/deep';
    mkdir($subdir, 0777, true);

    expect(GitVCS::isAvailable($subdir . '/file.php'))->toBeTrue();

    removeTempDir($root);
});

it('returns false when given an absolute path with no .git ancestor', function () {
    $root = makeTempDir();
    $subdir = $root . '/src/deep';
    mkdir($subdir, 0777, true);

    expect(GitVCS::isAvailable($subdir . '/file.php'))->toBeFalse();

    removeTempDir($root);
});

// --- SubversionVCS::isAvailable ---

it('returns false for a relative path with no SVN — does not loop', function () {
    $result = SubversionVCS::isAvailable('no/such/relative/path/file.php');
    expect($result)->toBeFalse();
});

it('detects .svn when given an absolute path inside an svn working copy', function () {
    $root = makeTempDir();
    mkdir($root . '/.svn');
    $subdir = $root . '/lib';
    mkdir($subdir, 0777, true);

    expect(SubversionVCS::isAvailable($subdir . '/file.php'))->toBeTrue();

    removeTempDir($root);
});

it('returns false when given an absolute path with no .svn ancestor', function () {
    $root = makeTempDir();

    expect(SubversionVCS::isAvailable($root . '/file.php'))->toBeFalse();

    removeTempDir($root);
});

// --- VCSFactory::detect ---

it('detect() returns null for a relative path with no VCS — does not loop', function () {
    $root = makeTempDir();
    $original = getcwd();
    chdir($root);

    $result = VCSFactory::detect('relative/path/file.php');

    chdir($original);
    removeTempDir($root);

    expect($result)->toBeNull();
});

it('detect() returns a GitVCS instance when a .git directory is present', function () {
    $root = makeTempDir();
    mkdir($root . '/.git');

    $vcs = VCSFactory::detect($root . '/file.php');
    expect($vcs)->toBeInstanceOf(GitVCS::class);

    removeTempDir($root);
});
