<?php

declare(strict_types=1);

namespace GoSuccess\Bunny\Core\Resource\Handwritten;

use GoSuccess\Bunny\Core\Resource\BillingResource;
use GoSuccess\Bunny\Http\Method;

/**
 * Hand-written methods of {@see BillingResource}.
 */
trait BillingOperations
{
    /**
     * Download the PDF summary of a billing record.
     *
     * `GET /billing/summary/{billingRecordId}/pdf`
     *
     * @param int $billingRecordId The ID of the billing record, see
     *                             {@see BillingResource::details()}.
     *
     * @return string The PDF document.
     */
    public function summaryPdf(int $billingRecordId): string
    {
        return $this->connection->send(Method::Get, "billing/summary/{$billingRecordId}/pdf", headers: ['Accept' => 'application/pdf'])->body;
    }

    /**
     * Download the invoice of a payment request as PDF.
     *
     * `GET /billing/payment-request-invoice/{id}/pdf`
     *
     * @param int $id The ID of the payment request, see
     *                {@see BillingResource::paymentRequests()}.
     *
     * @return string The PDF document.
     */
    public function paymentRequestInvoicePdf(int $id): string
    {
        return $this->connection->send(Method::Get, "billing/payment-request-invoice/{$id}/pdf", headers: ['Accept' => 'application/pdf'])->body;
    }
}
