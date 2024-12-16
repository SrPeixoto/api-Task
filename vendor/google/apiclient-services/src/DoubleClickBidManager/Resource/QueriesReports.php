<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\DoubleClickBidManager\Resource;

use Google\Service\DoubleClickBidManager\ListReportsResponse;
use Google\Service\DoubleClickBidManager\Report;

/**
 * The "reports" collection of methods.
 * Typical usage is:
 *  <code>
 *   $doubleclickbidmanagerService = new Google\Service\DoubleClickBidManager(...);
 *   $reports = $doubleclickbidmanagerService->queries_reports;
 *  </code>
 */
class QueriesReports extends \Google\Service\Resource
{
  /**
   * Retrieves a report. (reports.get)
   *
   * @param string $queryId Required. The ID of the query that generated the
   * report.
   * @param string $reportId Required. The ID of the query to retrieve.
   * @param array $optParams Optional parameters.
   * @return Report
   * @throws \Google\Service\Exception
   */
  public function get($queryId, $reportId, $optParams = [])
  {
    $params = ['queryId' => $queryId, 'reportId' => $reportId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Report::class);
  }
  /**
   * Lists reports generated by the provided query. (reports.listQueriesReports)
   *
   * @param string $queryId Required. The ID of the query that generated the
   * reports.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string orderBy Field to sort the list by. Accepts the following
   * values: * `key.reportId` (default) The default sorting order is ascending. To
   * specify descending order for a field, add the suffix `desc` to the field
   * name. For example, `key.reportId desc`.
   * @opt_param int pageSize Maximum number of results per page. Must be between
   * `1` and `100`. Defaults to `100` if unspecified.
   * @opt_param string pageToken A token identifying which page of results the
   * server should return. Typically, this is the value of nextPageToken returned
   * from the previous call to the `queries.reports.list` method. If unspecified,
   * the first page of results is returned.
   * @return ListReportsResponse
   * @throws \Google\Service\Exception
   */
  public function listQueriesReports($queryId, $optParams = [])
  {
    $params = ['queryId' => $queryId];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListReportsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueriesReports::class, 'Google_Service_DoubleClickBidManager_Resource_QueriesReports');