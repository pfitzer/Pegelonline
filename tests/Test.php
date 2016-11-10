<?php
/**
 * @author Michael Pfister <michael@mp-development.de>
 * @copyright (c) 2016, Michael Pfister
 * @license MIT
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF
 * ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
 * TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
 * OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
 * USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * https://opensource.org/licenses/MIT
 */
namespace Pfitzer\Pegelonline;

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit_Framework_TestCase;
use Pfitzer\Pegelonline;
use OtherCode\Rest;

class PegelonlineTest extends PHPUnit_Framework_TestCase {

    public function testConstruct() {
        $restMock = $this->getMockBuilder(Rest\Rest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pgOnline = new Pegelonline\Pegelonline($restMock);
        $this->assertInstanceOf(Rest\Rest::class, $pgOnline->rest);
    }

    public function testApiUrl() {
        $restMock = $this->getMockBuilder(Rest\Rest::class)
            ->disableOriginalConstructor()
            ->getMock();
        $pgOnline = new Pegelonline\Pegelonline($restMock);
        $this->assertEquals('https://www.pegelonline.wsv.de/webservices/rest-api/v2/', $pgOnline->getApiUrl());
    }
}
