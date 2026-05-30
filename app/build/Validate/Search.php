<?php

namespace Bruder\Validate;

class Search
{

  /**
   * Full Text Search variables serialized for boolean mode
   * searches. Replaces all boolean mode search operators with as
   * space and creates an array of all the words in the query in
   * following format: ["+*word*", "+*word*"];
   *
   * @param string $query
   * @return string
   */
  public static function ft_params_boolean_mode(string $query)
  {
    // $ft_mode = "IN BOOLEAN MODE";
    $ft_boolean_operators = ['@', '+', '-', '>', '<', '(', ')', '~', '*', '"'];

    $ft_boolean_filtered_query = str_replace($ft_boolean_operators, " ", $query);

    /**
     * Prepare the full text search for beatmaps by adding a plus
     * (increasing the weight) to any word specified in the query.
     */
    $query_words = explode(" ", trim($ft_boolean_filtered_query));
    $query_words_weighted = [];
    foreach ($query_words as $word) {
      $new_word = "+*$word*";

      if ($new_word && $new_word !== "+**")
        array_push($query_words_weighted, $new_word);
    }

    return implode(" ", $query_words_weighted);
  }
}
