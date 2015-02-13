<?php
/**
 * Swiss Payment Slip
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @copyright 2012-2015 Some nice Swiss guys
 * @author Manuel Reinhard <manu@sprain.ch>
 * @author Peter Siska <pesche@gridonic.ch>
 * @author Marc Würth ravage@bluewin.ch
 * @link https://github.com/sprain/class.Einzahlungsschein.php
 */

namespace SwissPaymentSlip\SwissPaymentSlip;

use SwissPaymentSlip\SwissPaymentSlip\Exception\DisabledDataException;

/**
 * Orange Swiss Payment Slip Data
 *
 * Creates data containers for standard Swiss payment slips with or without reference number.
 * It doesn't actually do much. It's mostly a data container class to keep
 * including classes from having to care about how ESR work.
 * But it provides a flexibility of which data it holds, because not always
 * all slip fields are needed in an application.
 *
 * Glossary:
 * ESR = Einzahlungsschein mit Referenznummer
 *         ISR, (In-)Payment slip with reference number
 *         Summary term for orange payment slips in Switzerland
 * BESR = Banken-Einzahlungsschein mit Referenznummer
 *         Banking payment slip with reference number
 *         Orange payment slip for paying into a bank account (in contrast to a post cheque account with a VESR)
 * VESR = Verfahren für Einzahlungsschein mit Referenznummer
 *         Procedure for payment slip with reference number
 *         Orange payment slip for paying into a post cheque account (in contrast to a banking account with a BESR)
 * (B|V)ESR+ = Einzahlungsschein mit Referenznummer ohne Betragsangabe
 *         Payment slip with reference number without amount specification
 *         An payment slip can be issued without a predefined payment amount
 * ES = Einzahlungsschein
 *         IS, (In-)Payment slip
 *         Also summary term for all payment slips.
 *         Red payment slip for paying into a post cheque or bank account without reference number, with message box
 *
 * @link https://www.postfinance.ch/content/dam/pf/de/doc/consult/manual/dlserv/inpayslip_isr_man_de.pdf German manual
 * @link http://www.six-interbank-clearing.com/en/home/standardization/dta.html
 */
class OrangePaymentSlipData extends PaymentSlipData
{

    /**
     * Determines if the payment slip has a reference number. Can be disabled for pre-printed payment slips
     *
     * @var bool True if yes, false if no.
     */
    protected $withReferenceNumber = true;

    /**
     * Determines if the payment slip's reference number should contain the banking customer ID.
     * Can be disabled for recipients who don't need this
     *
     * @var bool True if yes, false if no.
     */
    protected $withBankingCustomerId = true;

    /**
     * The reference number, without banking customer ID and check digit
     *
     * @var string The reference number.
     */
    protected $referenceNumber = '';

    /**
     * The banking customer ID, which will be prepended to the reference number
     *
     * @var string The banking customer ID.
     */
    protected $bankingCustomerId = '';

    /**
     * Set if payment slip has a reference number specified
     *
     * Resets reference number if disabled
     *
     * @param bool $withReferenceNumber True if yes, false if no.
     * @return $this The current instance for a fluent interface.
     */
    public function setWithReferenceNumber($withReferenceNumber = true)
    {
        if ($this->isBool($withReferenceNumber, 'withReferenceNumber')) {
            $this->withReferenceNumber = $withReferenceNumber;

            if (!$withReferenceNumber) {
                $this->referenceNumber = '';
            }
        }

        return $this;
    }

    /**
     * Get if payment slip has a reference number specified
     *
     * @return bool True if payment slip has a reference number specified, else false.
     */
    public function getWithReferenceNumber()
    {
        return $this->withReferenceNumber;
    }

    /**
     * Set if the payment slip's reference number should contain the banking customer ID
     *
     * @param bool $withBankingCustomerId True if successful, else false.
     * @return $this The current instance for a fluent interface.
     */
    public function setWithBankingCustomerId($withBankingCustomerId = true)
    {
        if ($this->isBool($withBankingCustomerId, 'withBankingCustomerId')) {
            $this->withBankingCustomerId = $withBankingCustomerId;

            if ($withBankingCustomerId === false) {
                $this->bankingCustomerId = '';
            }
        }

        return $this;
    }

    /**
     * Get if the payment slip's reference number should contain the banking customer ID.
     *
     * @return bool True if payment slip has the recipient specified, else false.
     */
    public function getWithBankingCustomerId()
    {
        return $this->withBankingCustomerId;
    }

    /**
     * Set the reference number
     *
     * @param string $referenceNumber The reference number.
     * @return $this The current instance for a fluent interface.
     * @throws DisabledDataException If the data is disabled.
     */
    public function setReferenceNumber($referenceNumber)
    {
        if (!$this->getWithReferenceNumber()) {
            throw new DisabledDataException('reference number');
        }
        // TODO validate reference number
        $this->referenceNumber = $referenceNumber;

        return $this;
    }

    /**
     * Get the reference number
     *
     * @return string The reference number, if withReferenceNumber is set to true.
     * @throws DisabledDataException If the data is disabled.
     */
    public function getReferenceNumber()
    {
        if (!$this->getWithReferenceNumber()) {
            throw new DisabledDataException('reference number');
        }
        return $this->referenceNumber;
    }

    /**
     * Set the banking customer ID
     *
     * @param string $bankingCustomerId The banking customer ID.
     * @return $this The current instance for a fluent interface.
     * @throws DisabledDataException If the data is disabled.
     */
    public function setBankingCustomerId($bankingCustomerId)
    {
        if (!$this->getWithBankingCustomerId()) {
            throw new DisabledDataException('banking customer ID');
        }
        // TODO check length (exactly 6)
        $this->bankingCustomerId = $bankingCustomerId;

        return $this;
    }

    /**
     * Get the banking customer ID
     *
     * @return string The  banking customer ID, if withBankingCustomerId is set to true.
     * @throws DisabledDataException If the data is disabled.
     */
    public function getBankingCustomerId()
    {
        if (!$this->getWithBankingCustomerId()) {
            throw new DisabledDataException('banking customer ID');
        }
        return $this->bankingCustomerId;
    }

    /**
     * Set payment slip for not to be used for payment
     *
     * XXXes out all fields to prevent people using the payment slip.
     *
     * @param boolean $notForPayment True if not for payment, else false.
     * @return $this The current instance for a fluent interface.
     */
    public function setNotForPayment($notForPayment = true)
    {
        parent::setNotForPayment($notForPayment);

        if ($notForPayment === true) {
            $this->setReferenceNumber('XXXXXXXXXXXXXXXXXXXX');
            $this->setBankingCustomerId('XXXXXX');
        }

        return $this;
    }

    /**
     * Get complete reference number
     *
     * @param bool $formatted Should the returned reference be formatted in blocks of five (for better readability).
     * @param bool $fillZeros Fill up with leading zeros, only applies to the case where no banking customer ID is used.
     * @return string The complete (with/without bank customer ID), formatted reference number with check digit
     * or false if withReferenceNumber is false.
     */
    public function getCompleteReferenceNumber($formatted = true, $fillZeros = true)
    {
        $referenceNumber = $this->getReferenceNumber();
        $notForPayment = $this->getNotForPayment();

        $completeReferenceNumber = $referenceNumber;
        if ($notForPayment) {
            $completeReferenceNumber =  str_pad($referenceNumber, 26, 'X', STR_PAD_LEFT);
        } elseif ($this->getWithBankingCustomerId()) {
            // Get reference number and fill with zeros
            $referenceNumber = str_pad($referenceNumber, 20, '0', STR_PAD_LEFT);
            // Prepend banking customer identification code
            $completeReferenceNumber = $this->getBankingCustomerId() . $referenceNumber;
        } elseif ($fillZeros) {
            // Get reference number and fill with zeros
            $completeReferenceNumber = str_pad($referenceNumber, 26, '0', STR_PAD_LEFT);
        }

        // Add check digit
        $completeReferenceNumber = $this->appendCheckDigit($completeReferenceNumber, $notForPayment);

        if ($formatted) {
            $completeReferenceNumber = $this->breakStringIntoBlocks($completeReferenceNumber);
        }

        return $completeReferenceNumber;
    }

    /**
     * Append the check digit to the reference number
     *
     * Simply appends an 'X' if the slip is not meant for payment.
     *
     * @param string $referenceNumber The reference number to calculate the prefix with.
     * @param bool $notForPayment Whether the payment slip is not ment for payment.
     * @return string The reference number with the appended check digit.
     */
    protected function appendCheckDigit($referenceNumber, $notForPayment = false)
    {
        if ($notForPayment === true) {
            return $referenceNumber . 'X';
        }
        return $referenceNumber . $this->modulo10($referenceNumber);
    }

    /**
     * Get the full code line at the bottom of the ESR
     *
     * @param bool $fillZeros Whether to fill up the code line with leading zeros.
     * @return string The full code line.
     */
    public function getCodeLine($fillZeros = true)
    {
        $referenceNumber = $this->getCompleteReferenceNumber(false, $fillZeros);
        $accountNumber = $this->getAccountDigits();

        if ($this->getWithAmount()) {
            $francs = $this->getAmountFrancs();
            $cents = $this->getAmountCents();
            $francs = str_pad($francs, 8, '0', STR_PAD_LEFT);
            $cents = str_pad($cents, 2, '0', STR_PAD_RIGHT);
            $amountPrefix = '01';
            $amountPart = $francs . $cents;
            $amountCheck = $this->modulo10($amountPrefix . $amountPart);
        } else {
            $amountPrefix = '04';
            $amountPart = '';
            $amountCheck = '2';
        }
        if ($fillZeros) {
            $referenceNumberPart = str_pad($referenceNumber, 27, '0', STR_PAD_LEFT);
        } else {
            $referenceNumberPart = $referenceNumber;
        }
        $accountNumberPart = substr($accountNumber, 0, 2) .
            str_pad(substr($accountNumber, 2), 7, '0', STR_PAD_LEFT);

        if ($this->getNotForPayment()) {
            $amountPrefix = 'XX';
            $amountCheck = 'X';
        }

        $codeLine = sprintf(
            '%s%s%s>%s+ %s>',
            $amountPrefix,
            $amountPart,
            $amountCheck,
            $referenceNumberPart,
            $accountNumberPart
        );

        return $codeLine;
    }
}