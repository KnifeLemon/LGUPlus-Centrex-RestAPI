<?php

namespace LGUPlus\Centrex\Models;

/**
 * 주소록 페이지 정보
 *
 * addressList() 응답의 LISTINFO 를 나타냅니다.
 */
class ListInfo
{
    /** 현재 페이지 번호 */
    public int $page;

    /** 전체 연락처 수 */
    public int $total;

    /** 페이지당 항목 수 */
    public int $numPerPage;

    /** 전체 페이지 수 (계산값) */
    public int $totalPages;

    public function __construct(int $page, int $total, int $numPerPage, int $totalPages)
    {
        $this->page        = $page;
        $this->total       = $total;
        $this->numPerPage  = $numPerPage;
        $this->totalPages  = $totalPages;
    }

    /**
     * API 응답 배열로부터 생성
     *
     * @param array<string, mixed> $data LISTINFO 배열
     */
    public static function fromArray(array $data): self
    {
        $page       = (int) ($data['page']       ?? 0);
        $total      = (int) ($data['total']      ?? 0);
        $numPerPage = (int) ($data['numperpage'] ?? 0);
        $totalPages = ($numPerPage > 0) ? max(1, (int) ceil($total / $numPerPage)) : 1;

        return new self($page, $total, $numPerPage, $totalPages);
    }

    /**
     * 주어진 페이지가 마지막 페이지인지 여부
     */
    public function isLastPage(int $currentPage): bool
    {
        return $currentPage >= $this->totalPages;
    }
}
