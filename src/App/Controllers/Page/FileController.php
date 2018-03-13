<?php

namespace App\Controllers\Page;

use App\Models\File;
use App\Controllers\BaseController;
use App\Transformers\FileTransformer;
use App\Exceptions\Error;

use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class FileController extends BaseController {

    const DELETED = 'the file has been deleted';

    protected $fs;

    public function __construct(ContainerInterface $container) {
        $this->fs = $container->get('fileSystem');
        parent::__construct($container);
    }

    /**
     * Get Collection of all Files in given Comment
     * @return Response
     */
    public function index(Request $request, Response $response, array $args) {
        if (!$user = $this->auth->requestUser($request)) {
            return Error::unauthorized($response);
        }
        if (!$page = $this->requestPage($args['guid'], $user)) {
            return Error::forbidden($response);
        }
        if (!$comment = $page->comment($args['commentId'])) {
            return Error::notFound($response);
        }
        $result = $this->resources($comment->files, new FileTransformer);
        return $this->render($response, $result);
    }

    /**
     * Create a New File
     * @return Response
     */
    public function create(Request $request, Response $response, array $args) {
        if (!$user = $this->auth->requestUser($request)) {
            return Error::unauthorized($response);
        }
        if (!$page = $this->requestPage($args['guid'], $user)) {
            return Error::forbidden($response);
        }
        if (!$comment = $page->comment($args['commentId'])) {
            return Error::notFound($response);
        }
        $uploadedFiles = $request->getUploadedFiles();
        $filesData = [];

        if (isset($uploadedFiles['files'])) {
            foreach ($uploadedFiles['files'] as $file) {
                if ($file->getError() !== UPLOAD_ERR_OK) {
                    continue;
                }
                $filesData[] = [
                    'path' => $this->fs->append($file, $page->guid),
                    'name' => $file->getClientFilename(),
                    'type' => $file->getClientMediaType(),
                    'comment_id' => $comment->id
                ];
            }
        }
        $files = File::createMany($filesData);
        $result = $this->resources($files, new FileTransformer);
        return $this->render($response, $result, 201);
    }

    /**
     * Delete a File
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args) {
        if (!$user = $this->auth->requestUser($request)) {
            return Error::unauthorized($response);
        }
        if (!$page = $this->requestPage($args['guid'], $user)) {
            return Error::forbidden($response);
        }
        if (!$comment = $page->comment($args['commentId'])) {
            return Error::notFound($response);
        }
        if ($file = $comment->file($args['fileId'])) {
            $this->fs->unlink($file->path);
            $file->delete();
        }
        return $this->render($response, self::DELETED, 200);
    }

}
