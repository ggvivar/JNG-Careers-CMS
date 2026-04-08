<?php if ($pager->getPageCount() > 1) : ?>
<style>
.pager-compact{
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap:8px;
    width:100%;
}
.pager-compact-info{
    font-size:12px;
    color:#6b7280;
    margin-right:4px;
}
.pager-compact-pages{
    display:flex;
    align-items:center;
    gap:4px;
    flex-wrap:wrap;
}
.pager-compact-btn,
.pager-compact-page{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:32px;
    height:32px;
    padding:0 10px;
    border:1px solid #d1d5db;
    border-radius:10px;
    background:#fff;
    color:#374151;
    text-decoration:none;
    font-size:13px;
    font-weight:500;
    line-height:1;
    box-shadow:none;
    transition:all .15s ease-in-out;
}
.pager-compact-btn:hover,
.pager-compact-page:hover{
    background:#f9fafb;
    border-color:#cbd5e1;
    color:#111827;
}
.pager-compact-active{
    background:#4f46e5;
    border-color:#4f46e5;
    color:#fff !important;
}
.pager-compact-disabled{
    background:#f3f4f6;
    border-color:#e5e7eb;
    color:#9ca3af;
    pointer-events:none;
    cursor:not-allowed;
}
</style>

<nav class="pager-compact" aria-label="<?= lang('Pager.pageNavigation') ?>">
    <span class="pager-compact-info">
        <?= $pager->getCurrentPageNumber() ?> / <?= $pager->getPageCount() ?>
    </span>

    <div class="pager-compact-pages">
        <?php if ($pager->hasPrevious()) : ?>
            <a href="<?= $pager->getPrevious() ?>" class="pager-compact-btn">Prev</a>
        <?php else : ?>
            <span class="pager-compact-btn pager-compact-disabled">Prev</span>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
            <?php if ($link['active']) : ?>
                <span class="pager-compact-page pager-compact-active"><?= $link['title'] ?></span>
            <?php else : ?>
                <a href="<?= $link['uri'] ?>" class="pager-compact-page"><?= $link['title'] ?></a>
            <?php endif ?>
        <?php endforeach ?>

        <?php if ($pager->hasNext()) : ?>
            <a href="<?= $pager->getNext() ?>" class="pager-compact-btn">Next</a>
        <?php else : ?>
            <span class="pager-compact-btn pager-compact-disabled">Next</span>
        <?php endif ?>
    </div>
</nav>
<?php endif ?>