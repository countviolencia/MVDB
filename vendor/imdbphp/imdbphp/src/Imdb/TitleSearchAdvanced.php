<?php

#############################################################################
# IMDBPHP                              (c) Giorgos Giagas & Itzchak Rehberg #
# ------------------------------------------------------------------------- #
# Miscellaneous movie lists                                                 #
# written by Itzchak Rehberg <izzysoft AT qumran DOT org>                   #
# http://www.izzysoft.de/                                                   #
# ------------------------------------------------------------------------- #
# This program is free software; you can redistribute and/or modify it      #
# under the terms of the GNU General Public License (see doc/LICENSE)       #
#############################################################################

namespace Imdb;

/**
 * Use IMDb's advanced search to get filtered lists of titles
 * e.g. most popular tv shows from 2000
 * @see http://www.imdb.com/search/
 * @see http://www.imdb.com/search/title?year=2015,2015&title_type=feature&explore=has
 * @author Izzy (izzysoft AT qumran DOT org)
 * @copyright (c) 2009 by Itzchak Rehberg and IzzySoft
 */
class TitleSearchAdvanced extends MdbBase {

  // Title types
  const MOVIE = 'feature';
  const TV_SERIES = 'tv_series';
  const TV_EPISODE = 'tv_episode';
  const TV_MINI_SERIES = 'mini_series';
  const TV_MOVIE = 'tv_movie';
  const TV_SPECIAL = 'tv_special';
  const DOCUMENTARY = 'documentary';
  const GAME = 'game';
  const VIDEO = 'video';
  const SHORT = 'short';

  // Sorts
  const SORT_MOVIEMETER = 'moviemeter';
  const SORT_ALPHA = 'alpha';
  const SORT_USER_RATING = 'user_rating';
  const SORT_NUM_VOTES = 'num_votes';
  const SORT_US_BOX_OFFICE_GROSS = 'boxoffice_gross_us';

  protected $titleTypes = array();
  protected $year = null;
  protected $countries = array();
  protected $languages = array();
  protected $sort = 'moviemeter';

  /**
   * Set which types of titles should be returned
   * @param array $types e.g. [self::MOVIE, self::DOCUMENTARY]
   */
  public function setTitleTypes(array $types) {
    $this->titleTypes = $types;
  }

  /**
   * Set which year you want titles from
   * @param int $year
   */
  public function setYear($year) {
    $this->year = $year;
  }

  /**
   * Set which countries of origin you want titles from
   * These are combinatory so you will only get titles that were made in every country you specify
   * @param array $countries Countries are 2/3/4 character codes
   * @see http://www.imdb.com/country/
   */
  public function setCountries(array $countries) {
    $this->countries = $countries;
  }

  /**
   * Set which languages are in the title
   * These are combinatory so you will only get titles that include every language you specify
   * @param array $languages Languages are 2/3/4 character codes
   * @see http://www.imdb.com/language/
   */
  public function setLanguages(array $languages) {
    $this->languages = $languages;
  }

  /**
   * Set the ordering of results.
   * See the SORT_ constants e.g. self::SORT_MOVIEMETER
   * @param string $sort
   */
  public function setSort($sort) {
    $this->sort = $sort;
  }

  /**
   * Perform the search
   * @return array
   * array('imdbid' => $id,
   *  'title' => $title,
   *  'year' => $year,
   *  'type' => $mtype,              e.g. 'TV Series', 'Feature Film' ..
   *  'serial' => $is_serial,        Is it a TV Series?
   *  'episode_imdbid' => $ep_id,    If the search found an episode it will show as type TV Series but have episode information too
   *  'episode_title' => $ep_name,   As above. The title of the episode
   *  'episode_year' => $ep_year     As above. The year of the episode
   * )
   */
  public function search() {
    $page = $this->getPage('');
    return $this->parse_results($page);
  }

  protected function buildUrl($context = null) {
    $queries = array();

    if ($this->titleTypes) {
      $queries['title_type'] = implode(',', $this->titleTypes);
    }

    if ($this->year) {
      $queries['year'] = $this->year;
    }

    if ($this->countries) {
      $queries['countries'] = implode(',', $this->countries);
    }

    if ($this->languages) {
      $queries['languages'] = implode(',', $this->languages);
    }

    if ($this->sort) {
      $queries['sort'] = $this->sort;
    }

    return "http://" . $this->imdbsite . '/search/title?' . http_build_query($queries);
  }

  /**
   * @param string html of page
   */
  protected function parse_results($page) {
    $doc = new \DOMDocument();
    @$doc->loadHTML($page);
    $xp = new \DOMXPath($doc);
    $titles = $xp->query("//div[@id='main']/table/tr/td[3]/a");
    $details = $xp->query("//div[@id='main']/table/tr/td[3]/span[2]");
    $serdet = $xp->query("//div[@id='main']/table/tr/td[3]/span[3]");
    $nodecount = $titles->length;
    $ret = array();
    for ($i = 0; $i < $nodecount;  ++$i) {
      preg_match('|(\d{7})/$|', $titles->item($i)->getAttribute('href'), $match);
      $id = $match[1];
      $title = trim($titles->item($i)->nodeValue);
      preg_match('!\((\d+)\s*(.*?)\)!', $details->item($i)->nodeValue, $match);
      $year = $match[1];
      $mtype = $match[2] ? : 'Feature Film';
      $is_serial = strpos(strtolower($mtype), 'tv series') !== false;
      $ep_year = '';
      $ep_id = 0;
      $ep_name = '';

      if ($is_serial && strpos($serdet->item($i)->nodeValue, 'Episode:') !== false) {
        preg_match('!\((\d{4})\)!', $serdet->item($i)->nodeValue, $match);
        if (isset($match[1])) {
          $ep_year = $match[1];
        }

        $episodeTitleNode = $serdet->item($i)->getElementsByTagName('a')->item(0);
        preg_match('!(\d{7})!', $episodeTitleNode->getAttribute('href'), $match);
        $ep_id = $match[1];
        $ep_name = trim($episodeTitleNode->nodeValue);
      }
      $ret[] = array('imdbid' => $id, 'title' => $title, 'year' => $year, 'type' => $mtype, 'serial' => $is_serial, 'episode_imdbid' => $ep_id, 'episode_title' => $ep_name, 'episode_year' => $ep_year);
    }
    return $ret;
  }

}
