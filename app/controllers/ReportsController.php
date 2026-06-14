<?php

namespace Heiakim\Controller;

use Heiakim\Controller\Controller;
use Heiakim\Model\Report;

class ReportsController extends Controller
{

  /**
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["reference_id", "report_type", "comment_string"],
      optional: ["user_notification"],
    );

    $this->authorize(respect_social_exclusion: true);

    # Reference object exists?
    $Reference = Report::find_reference_or_die(
      $this->params->report_type,
      $this->params->reference_id
    );

    # Create a Report.
    $Report = CurrentUser->reports()->make();
    $Report->reference_id = $Reference->id;
    $Report->report_type = $this->params->report_type;
    $Report->comment_string = $this->params->comment_string;
    $Report->user_notification = !empty($this->params->user_notification) ? 1 : 0;
    $Report->save();

    # Create notification if set.
    if ($this->params->user_notification)
      CurrentUser->notifications()
        ->create([
          "type" => $Report->notification_type(),
          "reference_id" => $Reference->id,
          "updated_at" => null,
        ]);

    return success("<strong>Your report has been created!</strong> Thank you for your effort to keep this a fun place 🙂");
  }
}
