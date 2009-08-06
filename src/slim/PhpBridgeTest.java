package slim;

import static org.junit.Assert.*;

import java.io.File;
import java.io.FileNotFoundException;
import java.lang.reflect.Proxy;

import javax.script.Invocable;

import org.junit.*;

public class PhpBridgeTest {
  private PhpBridge bridge;
  
  @Before
  public void setup() {
    bridge = new PhpBridge();
  }
  
  @Test
  public void get_internal_php_path() throws Exception {
    File path = new File(bridge.getInternalPhpPath());
    assertTrue(path.exists());
    assertTrue(path.isAbsolute());
    assertTrue(path.isDirectory());
    assertEquals("PhpSlim", path.getName());
  }

  @Test
  public void engine_is_of_correct_type() {
    assertEquals("php-invocable", bridge.getScriptEngine().getFactory().getLanguageName());
  }

  @Test
  public void get_php_statement_executor() throws Exception {
    Proxy statementExecutor = bridge.getStatementExecutor();
    assertNotNull(statementExecutor);
    Invocable inv = (Invocable) bridge.getScriptEngine();
    inv.invokeMethod(statementExecutor, "setSymbol", new Object[]{"name", "Bob"});
    Object symbol = inv.invokeMethod(statementExecutor, "getSymbol", new Object[]{"name"});
    assertEquals("Bob", symbol);
  }
  
}
