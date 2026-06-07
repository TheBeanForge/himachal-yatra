<?php
session_start();
if (empty($_SESSION['admin_user'])) { header('Location: login.php'); exit; }

/*
 * Leads are now captured solely by the booking calculator (booking_enquiries).
 * The old contact-form table (`bookings`) is retired, so this former "Leads"
 * dashboard simply forwards to the single, richer Enquiries inbox.
 * Status quick-filters are preserved via the ?status= query string.
 */
$qs = !empty($_GET['status']) ? '?status=' . urlencode($_GET['status']) : '';
header('Location: enquiries.php' . $qs);
exit;
