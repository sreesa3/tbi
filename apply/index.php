<?php
declare(strict_types=1);
require dirname(__DIR__) . '/includes/app.php';
tbi_start_session();
$error = $_SESSION['form_error'] ?? '';
$old = $_SESSION['form_old'] ?? [];
unset($_SESSION['form_error'], $_SESSION['form_old']);
$token = tbi_csrf_token();

function old(array $old, string $key, string $default = ''): string
{
    return tbi_h(isset($old[$key]) ? (string) $old[$key] : $default);
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="referrer" content="strict-origin-when-cross-origin" />
    <title>Incubatee application | KSRF TBI</title>
    <link rel="icon" href="../assets/logos/scsvmv-logo.png" type="image/png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@600;700&family=Poppins:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../styles/main.css" />
  </head>
  <body>
    <a class="skip-link" href="#main">Skip to content</a>
    <header class="site-header">
      <div class="container site-header__inner">
        <div class="brand">
          <a class="brand__home" href="../index.html">
            <img class="brand__mark" src="../assets/logos/scsvmv-logo.png" width="80" height="80" alt="" />
            <span class="brand__text">
              <span class="brand__uni">SCSVMV Deemed to be University</span>
              <span class="brand__name">Technology Business Incubator</span>
            </span>
          </a>
          <a class="brand__parent" href="https://ksrf.in" target="_blank" rel="noopener noreferrer"
            >A unit of Kanchi Shankara Research Foundation</a
          >
        </div>
        <nav aria-label="Page">
          <ul class="nav">
            <li><a href="../index.html">Home</a></li>
            <li><a href="../index.html#apply">How to apply</a></li>
          </ul>
        </nav>
      </div>
    </header>

    <main id="main">
      <section class="section">
        <div class="container form-wrap">
          <header class="section__head">
            <p class="section__label">Apply</p>
            <h2>Application form for incubatees</h2>
            <p class="section__intro">
              Same fields as the official TBI application. Do not upload PAN or Aadhaar.
              ID documents are collected only through official in-person channels.
            </p>
          </header>

          <?php if ($error !== ''): ?>
            <p class="form-alert" role="alert"><?php echo tbi_h($error); ?></p>
          <?php endif; ?>

          <form class="app-form" action="submit.php" method="post" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="csrf" value="<?php echo tbi_h($token); ?>" />
            <div class="hp" aria-hidden="true">
              <label>Leave blank <input type="text" name="website" tabindex="-1" autocomplete="off" /></label>
            </div>

            <fieldset>
              <legend>Applicant</legend>
              <div class="form-grid">
                <label class="span-2">Name of the entrepreneur *
                  <input name="entrepreneur_name" required maxlength="200" value="<?php echo old($old, 'entrepreneur_name'); ?>" />
                </label>
                <label>Age
                  <input name="age" type="number" min="16" max="99" value="<?php echo old($old, 'age'); ?>" />
                </label>
                <label>Date of birth
                  <input name="date_of_birth" type="date" value="<?php echo old($old, 'date_of_birth'); ?>" />
                </label>
                <label class="span-2">Address for communication *
                  <textarea name="communication_address" required rows="3"><?php echo old($old, 'communication_address'); ?></textarea>
                </label>
                <label class="span-2">Permanent address *
                  <textarea name="permanent_address" required rows="3"><?php echo old($old, 'permanent_address'); ?></textarea>
                </label>
                <label>Phone (res)
                  <input name="phone_res" maxlength="40" value="<?php echo old($old, 'phone_res'); ?>" />
                </label>
                <label>Phone (office)
                  <input name="phone_off" maxlength="40" value="<?php echo old($old, 'phone_off'); ?>" />
                </label>
                <label>Mobile *
                  <input name="phone_mobile" required maxlength="40" value="<?php echo old($old, 'phone_mobile'); ?>" />
                </label>
                <label>Email *
                  <input name="email" type="email" required maxlength="190" value="<?php echo old($old, 'email'); ?>" />
                </label>
              </div>
            </fieldset>

            <fieldset>
              <legend>Educational qualification</legend>
              <div id="edu-rows" class="repeat-block">
                <?php for ($i = 0; $i < 2; $i++): ?>
                <div class="repeat-row">
                  <label>Class / course <input name="edu_course[]" maxlength="160" /></label>
                  <label>College / institution <input name="edu_college[]" maxlength="200" /></label>
                  <label>Branch <input name="edu_branch[]" maxlength="160" /></label>
                  <label>University / board <input name="edu_board[]" maxlength="160" /></label>
                  <label>Year of pass <input name="edu_year[]" maxlength="20" /></label>
                  <label>% secured <input name="edu_percent[]" maxlength="20" /></label>
                </div>
                <?php endfor; ?>
              </div>
              <button type="button" class="btn btn--outline" data-add="edu">Add another qualification</button>
            </fieldset>

            <fieldset>
              <legend>Skills and business</legend>
              <div class="form-grid">
                <label class="span-2">Innovative skills &amp; experience
                  <textarea name="skills_experience" rows="3"><?php echo old($old, 'skills_experience'); ?></textarea>
                </label>
                <label>Type of business
                  <input name="type_of_business" maxlength="200" value="<?php echo old($old, 'type_of_business'); ?>" />
                </label>
                <label>Name of the organization
                  <input name="organization_name" maxlength="200" value="<?php echo old($old, 'organization_name'); ?>" />
                </label>
              </div>
            </fieldset>

            <fieldset>
              <legend>Product / venture</legend>
              <div class="form-grid">
                <label class="span-2">Brief description of product and business / service *
                  <textarea name="product_description" required rows="5"><?php echo old($old, 'product_description'); ?></textarea>
                </label>
                <label>Start-up year
                  <input name="startup_year" maxlength="10" value="<?php echo old($old, 'startup_year'); ?>" />
                </label>
                <label class="span-2">Why you want to become an entrepreneur
                  <textarea name="why_entrepreneur" rows="5"><?php echo old($old, 'why_entrepreneur'); ?></textarea>
                </label>
                <label>Legal position
                  <select name="legal_position">
                    <option value="">Select</option>
                    <?php
                    foreach (['Proprietorship', 'Partnership', 'Corporation', 'Private'] as $lp) {
                        $sel = (($old['legal_position'] ?? '') === $lp) ? ' selected' : '';
                        echo '<option' . $sel . '>' . tbi_h($lp) . '</option>';
                    }
                    ?>
                  </select>
                </label>
              </div>
            </fieldset>

            <fieldset>
              <legend>Services expected from SCSVMV-TBI</legend>
              <div class="check-grid">
                <?php
                $services = [
                  'Workspace',
                  'Shared office services',
                  'Access to specialized equipment',
                  'Management assistance',
                  'Business planning',
                  'Access to finance',
                  'Technical assistance (Testing & Quality control etc.)',
                  'Networking support',
                  'Branding and marketing',
                  'Patenting',
                  'Mentoring / Counselling',
                  'Technology Upgradation (R & D) / Value Addition',
                ];
                foreach ($services as $s) {
                    echo '<label class="check"><input type="checkbox" name="services[]" value="' . tbi_h($s) . '" /> ' . tbi_h($s) . '</label>';
                }
                ?>
              </div>
            </fieldset>

            <fieldset>
              <legend>Team</legend>
              <div class="form-grid">
                <label class="span-2">Details of your team
                  <textarea name="team_details" rows="3"><?php echo old($old, 'team_details'); ?></textarea>
                </label>
                <label>Resident employees — full-time
                  <input name="employees_fulltime" maxlength="20" value="<?php echo old($old, 'employees_fulltime'); ?>" />
                </label>
                <label>Part-time
                  <input name="employees_parttime" maxlength="20" value="<?php echo old($old, 'employees_parttime'); ?>" />
                </label>
                <label>Consultants
                  <input name="employees_consultants" maxlength="20" value="<?php echo old($old, 'employees_consultants'); ?>" />
                </label>
                <label>Employees in the organization
                  <input name="employees_org" maxlength="20" value="<?php echo old($old, 'employees_org'); ?>" />
                </label>
              </div>
            </fieldset>

            <fieldset>
              <legend>Promoter details</legend>
              <div class="form-grid">
                <label>Name of the promoter
                  <input name="promoter_name" maxlength="200" value="<?php echo old($old, 'promoter_name'); ?>" />
                </label>
                <label>Educational qualification
                  <input name="promoter_qualification" maxlength="200" value="<?php echo old($old, 'promoter_qualification'); ?>" />
                </label>
                <label>Designation
                  <input name="promoter_designation" maxlength="200" value="<?php echo old($old, 'promoter_designation'); ?>" />
                </label>
                <label>Years of experience
                  <input name="promoter_experience" maxlength="80" value="<?php echo old($old, 'promoter_experience'); ?>" />
                </label>
                <label class="span-2">Address for communication
                  <textarea name="promoter_communication_address" rows="2"><?php echo old($old, 'promoter_communication_address'); ?></textarea>
                </label>
                <label class="span-2">Permanent address
                  <textarea name="promoter_permanent_address" rows="2"><?php echo old($old, 'promoter_permanent_address'); ?></textarea>
                </label>
                <label>Phone (res)
                  <input name="promoter_phone_res" maxlength="40" value="<?php echo old($old, 'promoter_phone_res'); ?>" />
                </label>
                <label>Phone (office)
                  <input name="promoter_phone_off" maxlength="40" value="<?php echo old($old, 'promoter_phone_off'); ?>" />
                </label>
                <label>Mobile
                  <input name="promoter_phone_mobile" maxlength="40" value="<?php echo old($old, 'promoter_phone_mobile'); ?>" />
                </label>
                <label>Email
                  <input name="promoter_email" type="email" maxlength="190" value="<?php echo old($old, 'promoter_email'); ?>" />
                </label>
                <label>Fax
                  <input name="promoter_fax" maxlength="40" value="<?php echo old($old, 'promoter_fax'); ?>" />
                </label>
              </div>
            </fieldset>

            <fieldset>
              <legend>Estimated project cost</legend>
              <div id="cost-rows" class="repeat-block">
                <?php for ($i = 0; $i < 3; $i++): ?>
                <div class="repeat-row repeat-row--2">
                  <label>Item <input name="cost_item[]" maxlength="200" /></label>
                  <label>Amount (₹) <input name="cost_amount[]" maxlength="40" /></label>
                </div>
                <?php endfor; ?>
              </div>
              <button type="button" class="btn btn--outline" data-add="cost">Add cost row</button>
            </fieldset>

            <fieldset>
              <legend>Reference 1</legend>
              <div class="form-grid">
                <label>Name <input name="ref1_name" maxlength="200" value="<?php echo old($old, 'ref1_name'); ?>" /></label>
                <label>Designation <input name="ref1_designation" maxlength="160" value="<?php echo old($old, 'ref1_designation'); ?>" /></label>
                <label class="span-2">Address <textarea name="ref1_address" rows="2"><?php echo old($old, 'ref1_address'); ?></textarea></label>
                <label>Phone <input name="ref1_phone" maxlength="40" value="<?php echo old($old, 'ref1_phone'); ?>" /></label>
                <label>Email <input name="ref1_email" type="email" maxlength="190" value="<?php echo old($old, 'ref1_email'); ?>" /></label>
              </div>
            </fieldset>

            <fieldset>
              <legend>Reference 2</legend>
              <div class="form-grid">
                <label>Name <input name="ref2_name" maxlength="200" value="<?php echo old($old, 'ref2_name'); ?>" /></label>
                <label>Designation <input name="ref2_designation" maxlength="160" value="<?php echo old($old, 'ref2_designation'); ?>" /></label>
                <label class="span-2">Address <textarea name="ref2_address" rows="2"><?php echo old($old, 'ref2_address'); ?></textarea></label>
                <label>Phone <input name="ref2_phone" maxlength="40" value="<?php echo old($old, 'ref2_phone'); ?>" /></label>
                <label>Email <input name="ref2_email" type="email" maxlength="190" value="<?php echo old($old, 'ref2_email'); ?>" /></label>
              </div>
            </fieldset>

            <fieldset>
              <legend>Uploads (optional)</legend>
              <p class="form-hint">Photograph max 2&nbsp;MB (JPG/PNG). Write-up max 5&nbsp;MB (PDF/DOC). No PAN or Aadhaar.</p>
              <div class="form-grid">
                <label>Promoter photograph
                  <input type="file" name="photo" accept="image/jpeg,image/png" />
                </label>
                <label>Product / business write-up
                  <input type="file" name="writeup" accept=".pdf,.doc,.docx,application/pdf" />
                </label>
              </div>
            </fieldset>

            <fieldset>
              <legend>Declaration</legend>
              <div class="form-grid">
                <label>Place *
                  <input name="place" required maxlength="120" value="<?php echo old($old, 'place'); ?>" />
                </label>
              </div>
              <details class="rules">
                <summary>Rules &amp; regulations for incubatees</summary>
                <ol>
                  <li>Registration is permitted only if the idea is innovative, relevant and/or commercially viable.</li>
                  <li>Incubatee registration fee of ₹5,000 makes the incubatee eligible for office space and lab/workshop use rights.</li>
                  <li>Pre-incubation support may be offered with a nominal fee for proof-of-concept work.</li>
                  <li>Lab and office rentals are payable as per TBI guidelines, typically quarterly.</li>
                  <li>Preferences on TBI initiatives depend on idea strength, progress, conduct, and social commitment.</li>
                  <li>Reference letters require a written request and expert review. Funding recommendations follow committee screening.</li>
                  <li>TBI does not shoulder financial responsibility for un-announced schemes.</li>
                  <li>Facilities are available during SCSVMV office hours unless permission is granted for extended use.</li>
                  <li>Facilities and the TBI brand may be used only for the intended purpose, with written intimation for brand use.</li>
                  <li>Exit is based on tenure (typically 2–3 years), milestones, business sustenance, and related facts.</li>
                </ol>
              </details>
              <label class="check check--block">
                <input type="checkbox" name="agreed_rules" value="1" required />
                I have read the rules and regulations and agree to abide by them. *
              </label>
            </fieldset>

            <div class="cta-row">
              <button type="submit" class="btn btn--solid">Submit application</button>
              <a class="btn btn--outline" href="../assets/docs/application-form.pdf" target="_blank" rel="noopener noreferrer">Download PDF</a>
            </div>
          </form>
        </div>
      </section>
    </main>
    <template id="edu-tpl">
      <div class="repeat-row">
        <label>Class / course <input name="edu_course[]" maxlength="160" /></label>
        <label>College / institution <input name="edu_college[]" maxlength="200" /></label>
        <label>Branch <input name="edu_branch[]" maxlength="160" /></label>
        <label>University / board <input name="edu_board[]" maxlength="160" /></label>
        <label>Year of pass <input name="edu_year[]" maxlength="20" /></label>
        <label>% secured <input name="edu_percent[]" maxlength="20" /></label>
      </div>
    </template>
    <template id="cost-tpl">
      <div class="repeat-row repeat-row--2">
        <label>Item <input name="cost_item[]" maxlength="200" /></label>
        <label>Amount (₹) <input name="cost_amount[]" maxlength="40" /></label>
      </div>
    </template>
    <script>
      (function () {
        document.querySelectorAll("[data-add]").forEach(function (btn) {
          btn.addEventListener("click", function () {
            var kind = btn.getAttribute("data-add");
            var tpl = document.getElementById(kind + "-tpl");
            var host = document.getElementById(kind + "-rows");
            if (!tpl || !host) return;
            host.appendChild(tpl.content.cloneNode(true));
          });
        });
      })();
    </script>
  </body>
</html>
