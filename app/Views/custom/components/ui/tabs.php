<?php $sections = $sections ?? []; ?>

<?php if (count($sections) > 1): ?>
    <div class="form-tabs">
        <?php foreach ($sections as $index => $section): ?>
            <button
                type="button"
                class="form-tab-btn <?= $index === 0 ? 'active' : '' ?>"
                data-tab-target="<?= esc($section['id'] ?? ('section-' . $index)) ?>"
            >
                <?= esc($section['title'] ?? ('Section ' . ($index + 1))) ?>
            </button>
        <?php endforeach; ?>
    </div>
<?php endif; ?>