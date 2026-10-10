<?php
# MantisBT - A PHP based bugtracking system

# MantisBT is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# MantisBT is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with MantisBT.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Test cases for the GPC API's array getters.
 *
 * search.php reads plugin filter fields of type FILTER_TYPE_MULTI_STRING /
 * FILTER_TYPE_MULTI_INT with gpc_get_string_array() / gpc_get_int_array().
 * A parameter that is not in the request must give an empty array there,
 * which these getters only do when the default is passed explicitly.
 *
 * @package    Tests
 * @subpackage GPC API
 * @copyright Copyright 2002  MantisBT Team - mantisbt-dev@lists.sourceforge.net
 * @link https://www.mantisbt.org
 */

namespace Mantis\tests\Mantis;

use Mantis\Exceptions\ClientException;

/**
 * PHPUnit tests for GPC API array getters
 */
final class GpcApiTest extends MantisCoreBase {

	const VAR_NAME = 'gpc_api_test_var';

	protected function setUp(): void {
		unset( $_GET[self::VAR_NAME], $_POST[self::VAR_NAME] );
	}

	protected function tearDown(): void {
		unset( $_GET[self::VAR_NAME], $_POST[self::VAR_NAME] );
	}

	/**
	 * The array getters under test.
	 * @return array
	 */
	public static function providerArrayGetters(): array {
		return [
			'string array' => [ 'gpc_get_string_array' ],
			'int array' => [ 'gpc_get_int_array' ],
		];
	}

	/**
	 * An absent parameter with an explicit default gives that default.
	 *
	 * @dataProvider providerArrayGetters
	 * @param string $p_function Getter.
	 */
	public function testAbsentWithExplicitDefault( string $p_function ): void {
		$this->assertSame( [], $p_function( self::VAR_NAME, [] ) );
	}

	/**
	 * An absent parameter without a default is an error, although the
	 * signature declares `array $p_default = []`.
	 *
	 * @dataProvider providerArrayGetters
	 * @param string $p_function Getter.
	 */
	public function testAbsentWithoutDefault( string $p_function ): void {
		$this->expectException( ClientException::class );
		$this->expectExceptionCode( ERROR_GPC_VAR_NOT_FOUND );
		$p_function( self::VAR_NAME );
	}

	/**
	 * A given parameter is read whatever the default.
	 */
	public function testGivenValues(): void {
		$_GET[self::VAR_NAME] = [ '1', '2' ];
		$this->assertSame( [ 1, 2 ], gpc_get_int_array( self::VAR_NAME, [] ) );
		$this->assertSame( [ '1', '2' ], gpc_get_string_array( self::VAR_NAME, [] ) );
	}
}
