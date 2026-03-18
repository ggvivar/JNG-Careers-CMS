<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApplicantAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (! preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return service('response')->setStatusCode(401)->setJSON([
                'status' => false,
                'message' => 'Authorization token missing.',
            ]);
        }

        $token = trim($matches[1]);

        $row = db_connect()->table('applicant_tokens t')
            ->select('a.*')
            ->join('applicants a', 'a.id = t.applicant_id')
            ->where('t.token', $token)
            ->where('t.revoked_at', null)
            ->groupStart()
                ->where('t.expires_at', null)
                ->orWhere('t.expires_at >', date('Y-m-d H:i:s'))
            ->groupEnd()
            ->where('a.date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $row) {
            return service('response')->setStatusCode(401)->setJSON([
                'status' => false,
                'message' => 'Invalid or expired token.',
            ]);
        }

        service('request')->applicant = $row;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}