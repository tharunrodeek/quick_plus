<?php
$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
$barcodeImage = base64_encode($generator->getBarcode($srv_req['barcode'], $generator::TYPE_CODE_128));
?>
<body>
    <img src="<?= dirname(__DIR__) ?>/company/0/images/pdf-header-top.jpg" class="w-100">
    <table class="w-100 table-sm mt-4">
        <tr>
            <td class="border w-50">
                <table class="w-100">
                    <tbody>
                        <tr>
                            <td colspan="2"><b>SERVICE REQUEST</b></td>
                        </tr>
                        <tr>
                            <td><b>Req Ref.</b></td>
                            <td><?= $srv_req['reference'] ?></td>
                        </tr>
                        <tr>
                            <td><b>Token No.</b></td>
                            <td><?= $srv_req['token_number'] ?></td>
                        </tr>
                        <tr>
                            <td><b>Barcode</b></td>
                            <td><img height="40" width="80"
                                     src="data:image/png;base64,<?= $barcodeImage ?>">
                                &nbsp;&nbsp;<p style="text-align: center;"><?= $srv_req['barcode'] ?></p></td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td class="border w-50">
                <table class="w-100">
                    <tbody>
                        <tr>
                            <td><b>Date - <span lang="ar">التاريخ والوقت</span></b></td>
                            <td><?= $srv_req['created_at'] ?></td>
                        </tr>
                        <tr>
                            <td><b>Customer - <span lang="ar">المتعامل</span></b></td>
                            <td><?= $srv_req['display_customer'] ?></td>
                        </tr>
                        <tr>
                            <td><b>Mobile No. - <span lang="ar">رقم الهاتف المتحرك</span></b></td>
                            <td><?= $srv_req['mobile'] ?></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <h3>Details Of Invoice</h3>
    <table class="table-sm table-bordered w-100">
        <thead>
            <tr>
                <th style="width: 8%;">Sl. No.<br><span lang="ar">الرقم</span></th>
                <th style="width: 46%;">Service<br><span lang="ar">الخدمات</span></th>
                <th style="width: 8%;">Qty<br><span lang="ar">الكمية</span></th>
                <th style="width: 10%;">Fee<br><span lang="ar">الرسوم</span></th>
                <th style="width: 10%;">Srv. Charge<br><span lang="ar">سعر الخدمة</span></th>
                <th style="width: 8%;">Tax Amt.<br><span lang="ar">قيمة المضافة</span></th>
                <th style="width: 10%;">Total<br><span lang="ar">الاجمالى بالدرهم</span></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($srv_req['_items'] as $i => $line): ?>
            <tr>
                <td class="text-center"><?= $i + 1 ?></td>
                <td>
                    <?= $line['description'] ?>
                    <?php if(!empty($line['_extra'])): ?>
                        <br><?= $line['_extra'] ?>
                    <?php endif ?>
                </td>
                <td class="text-center"><?= $line['qty'] ?></td>
                <td class="text-right"><?= number_format2($line['_fee'],2) ?></td>
                <td class="text-right"><?= number_format2($line['price'],2) ?></td>
                <td class="text-right"><?= number_format2($line['unit_tax'],2) ?></td>
                <td class="text-right"><?= number_format2($line['_total'],2) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot class="text-right">
            <tr>
                <td colspan="6"><b>Gross Amount <span lang="ar">المبلغ الإجمالي</span></b></td>
                <td><?= number_format2($srv_req['_gross_amt'],2) ?></td>
            </tr>
            <tr>
                <td colspan="6"><b>Discount <span lang="ar">خصم</span></b></td>
                <td><?=  number_format2($srv_req['_discount_amt'],2) ?></td>
            </tr>
            <tr>
                <td colspan="6"><b>Net Amount <span lang="ar">كمية الشبكة</span></b></td>
                <td><?=  number_format2($srv_req['_net_amt'] ,2)?></td>
            </tr>
        </tfoot>
    </table>
    <div class="w-100 text-right text-muted small py-1"><i>Entered by: <?= $srv_req['employee'] ?></div>

    <div style="height: 20mm;"></div>
    <div class="w-100 fixed-bottom">
        <img src="<?= dirname(__DIR__) ?>/company/0/images/pdf-footer-image.jpg">
    </div>
</body>