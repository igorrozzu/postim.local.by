<div class="selected-field btns-field">
    <div class="container-btns-field">
        <?php if ($post->is_open && $post->timeOpenOrClosed == null): ?>
            <div class="btn-all-along btn-time_work select-all active">Круглосуточно</div>
        <?php else: ?>
            <div class="btn-all-along btn-time_work select-all">Круглосуточно</div>
        <?php endif; ?>
        <?php if (!$post->is_open && $post->timeOpenOrClosed == null): ?>
            <div class="btn-all-along btn-time_work unselect-all active">Закрыто</div>
        <?php else: ?>
            <div class="btn-all-along btn-time_work unselect-all">Закрыто</div>
        <?php endif; ?>
        <div style="display: none" class="input-time-work-btn">
            <?php if ($post->is_open && $post->timeOpenOrClosed == null): ?>
                <input name="time_work[btns]" value="select">
            <?php elseif (!$post->is_open && $post->timeOpenOrClosed == null): ?>
                <input name="time_work[btns]" value="unselect">
            <?php else: ?>
                <input name="time_work[btns]" value="">
            <?php endif; ?>
        </div>
    </div>
    <div data-open-id="select-worktime" class="close-select-field"></div>
</div>
<?php
$timeWork = \app\components\Helper::parserWorktime($post->workingHours);
?>
<div id="select-worktime" style="margin-top: 0px;" class="open-select">
    <div class="container-row-time-work">
        <div class="day-name">Понедельник</div>
        <div class="day-name-min">Пн.</div>
        <div class="container-time">
            <span>с</span>
            <div class="time-period"><input name="time_work[1][start]"
                                            placeholder="00:00" <?= $timeWork[1]['time_start'] != null ? 'value="' . $timeWork[1]['time_start'] . '"' : '' ?>>
            </div>
            <span>до</span>
            <div class="time-period"><input name="time_work[1][finish]"
                                            placeholder="00:00" <?= $timeWork[1]['time_finish'] != null ? 'value="' . $timeWork[1]['time_finish'] . '"' : '' ?>>
            </div>
        </div>
    </div>
    <div class="container-row-time-work">
        <div class="day-name">Вторник</div>
        <div class="day-name-min">Вт.</div>
        <div class="container-time">
            <span>с</span>
            <div class="time-period"><input name="time_work[2][start]"
                                            placeholder="00:00" <?= $timeWork[2]['time_start'] != null ? 'value="' . $timeWork[2]['time_start'] . '"' : '' ?>>
            </div>
            <span>до</span>
            <div class="time-period"><input name="time_work[2][finish]"
                                            placeholder="00:00" <?= $timeWork[2]['time_finish'] != null ? 'value="' . $timeWork[2]['time_finish'] . '"' : '' ?>>
            </div>
        </div>
    </div>
    <div class="container-row-time-work">
        <div class="day-name">Среда</div>
        <div class="day-name-min">Ср.</div>
        <div class="container-time">
            <span>с</span>
            <div class="time-period"><input name="time_work[3][start]"
                                            placeholder="00:00" <?= $timeWork[3]['time_start'] != null ? 'value="' . $timeWork[3]['time_start'] . '"' : '' ?>>
            </div>
            <span>до</span>
            <div class="time-period"><input name="time_work[3][finish]"
                                            placeholder="00:00" <?= $timeWork[3]['time_finish'] != null ? 'value="' . $timeWork[3]['time_finish'] . '"' : '' ?>>
            </div>
        </div>
    </div>
    <div class="container-row-time-work">
        <div class="day-name">Четверг</div>
        <div class="day-name-min">Чт.</div>
        <div class="container-time">
            <span>с</span>
            <div class="time-period"><input name="time_work[4][start]"
                                            placeholder="00:00" <?= $timeWork[4]['time_start'] != null ? 'value="' . $timeWork[4]['time_start'] . '"' : '' ?>>
            </div>
            <span>до</span>
            <div class="time-period"><input name="time_work[4][finish]"
                                            placeholder="00:00" <?= $timeWork[4]['time_finish'] != null ? 'value="' . $timeWork[4]['time_finish'] . '"' : '' ?>>
            </div>
        </div>
    </div>
    <div class="container-row-time-work">
        <div class="day-name">Пятница</div>
        <div class="day-name-min">Пт.</div>
        <div class="container-time">
            <span>с</span>
            <div class="time-period"><input name="time_work[5][start]"
                                            placeholder="00:00" <?= $timeWork[5]['time_start'] != null ? 'value="' . $timeWork[5]['time_start'] . '"' : '' ?>>
            </div>
            <span>до</span>
            <div class="time-period"><input name="time_work[5][finish]"
                                            placeholder="00:00" <?= $timeWork[5]['time_finish'] != null ? 'value="' . $timeWork[5]['time_finish'] . '"' : '' ?>>
            </div>
        </div>
    </div>
    <div class="container-row-time-work">
        <div class="day-name">Суббота</div>
        <div class="day-name-min">Сб.</div>
        <div class="container-time">
            <span>с</span>
            <div class="time-period"><input name="time_work[6][start]"
                                            placeholder="00:00" <?= $timeWork[6]['time_start'] != null ? 'value="' . $timeWork[6]['time_start'] . '"' : '' ?>>
            </div>
            <span>до</span>
            <div class="time-period"><input name="time_work[6][finish]"
                                            placeholder="00:00" <?= $timeWork[6]['time_finish'] != null ? 'value="' . $timeWork[6]['time_finish'] . '"' : '' ?>>
            </div>
        </div>
    </div>
    <div class="container-row-time-work">
        <div class="day-name">Воскресенье</div>
        <div class="day-name-min">Вск.</div>
        <div class="container-time">
            <span>с</span>
            <div class="time-period"><input name="time_work[7][start]"
                                            placeholder="00:00" <?= $timeWork[7]['time_start'] != null ? 'value="' . $timeWork[2]['time_start'] . '"' : '' ?>>
            </div>
            <span>до</span>
            <div class="time-period"><input name="time_work[7][finish]"
                                            placeholder="00:00" <?= $timeWork[7]['time_finish'] != null ? 'value="' . $timeWork[7]['time_finish'] . '"' : '' ?>>
            </div>
        </div>
    </div>
</div>